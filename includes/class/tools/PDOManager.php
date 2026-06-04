<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */


    //---------------------------------------------------------  
	// CLASS PDO
	//---------------------------------------------------------

        namespace tools;

        use PDO;
        use PDOStatement;
        use PDOException;

        class PDOManager extends PDO {
            protected int $count = 0;
            protected array $memoryQuery = [];
            protected float $time = 0;

            // -----------------------
            // GETTERS
            // -----------------------
            public function count(): int { return $this->count; }
            public function memoryQuery(): array { return $this->memoryQuery; }
            public function time(): float { return $this->time; }

            public function increment(): void { $this->count++; }

            public function addQuery(string $query, float $time = 0): void
            {
                $this->memoryQuery[$this->count] = [
                    'query' => $query,
                    'time'  => $time
                ];
            }

            public function addTime(float $time): void
            {
                $this->time += $time;
            }

            // -----------------------
            // CONSTRUCTOR
            // -----------------------
            public function __construct(
                string $dsn,
                string $username = "",
                string $password = "",
                array $options = []
            ) {
                // UTF8 propre : uniquement dans le DSN (PAS de concat fragile)
                if (stripos($dsn, 'charset=') === false) {
                    $dsn .= ';charset=utf8mb4';
                }

                // Options PDO recommandées
                $options += [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                ];

                parent::__construct($dsn, $username, $password, $options);

                // Statement custom
                $this->setAttribute(
                    PDO::ATTR_STATEMENT_CLASS,
                    [myPDOStatement::class, [$this]]
                );
            }

            // -----------------------
            // QUERY TRACKING
            // -----------------------
            public function runQuery(string $query): PDOStatement|false
            {
                $start = microtime(true);

                $stmt = parent::query($query);

                $this->logQuery($query, $start);

                return $stmt;
            }

            public function execQuery(string $query): int|false
            {
                $start = microtime(true);

                $result = parent::exec($query);

                $this->logQuery($query, $start);

                return $result;
            }

            private function logQuery(string $query, float $start): void
            {
                $duration = round(microtime(true) - $start, 5);

                $this->addQuery($query, $duration);
                $this->addTime($duration);
                $this->increment();
            }
        }

        // ======================================================

        class myPDOStatement extends PDOStatement {
            protected PDOManager $pdo;

            protected function __construct(PDOManager $pdo)
            {
                $this->pdo = $pdo;
            }

            // Hook automatique sur execute()
            public function execute($params = null): bool
            {
                $start = microtime(true);

                $result = parent::execute($params);

                $duration = round(microtime(true) - $start, 5);

                $this->pdo->addQuery($this->queryString, $duration);
                $this->pdo->addTime($duration);
                $this->pdo->increment();

                return $result;
            }

            // -----------------------
            // HELPERS SAFE UTF-8 (optionnel)
            // -----------------------

                public function fetchFixedObj(): ?object {
                    $obj = $this->fetch(PDO::FETCH_OBJ);
                    if (!$obj) return null;

                    foreach ($obj as $key => $val) {
                        if (is_string($val)) {
                            $obj->$key = mb_check_encoding($val, 'UTF-8')
                                ? $val
                                : mb_convert_encoding($val, 'UTF-8', 'ISO-8859-1');
                        }
                    }
                    return $obj;
                }

                public function fetchAllFixedObj(): array {
                    $rows = $this->fetchAll(PDO::FETCH_OBJ);

                    foreach ($rows as &$obj) {
                        foreach ($obj as $key => $val) {
                            if (is_string($val)) {
                                $obj->$key = mb_check_encoding($val, 'UTF-8')
                                    ? $val
                                    : mb_convert_encoding($val, 'UTF-8', 'ISO-8859-1');
                            }
                        }
                    }
                    return $rows;
                }
        }