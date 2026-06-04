<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

    //---------------------------------------------------------  
	// CLASS STRING MANAGER
	//---------------------------------------------------------

    namespace Front;
    use PDO;
    use InvalidArgumentException;

   class StringManager {
        protected PDO $pdo;
        protected array $opts = [
            'table_tags' => 'param_tags'
        ];
        private array $translations;

        //---------------------------------------------------------
        // UTILITAIRES
        //--------------------------------------------------------- 

            protected function validateTableName(string $n): string {
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $n)) {
                    throw new InvalidArgumentException("Nom de table invalide");
                }
                return $n;
            }

        //---------------------------------------------------------
        // CONSTRUCT
        //---------------------------------------------------------

            public function __construct(PDO $pdo, array $translations = [], array $options = []){

                $this->pdo = $pdo;
                $this->opts = array_merge($this->opts, $options);

                $this->opts['table_tags'] = $this->validateTableName($this->opts['table_tags']);

                $this->translations = $translations;
            }


        //---------------------------------------------------------
        // CONSTRUCT
        //---------------------------------------------------------

            public function clean(string $v): string {
                return trim(htmlspecialchars($v, ENT_QUOTES, 'UTF-8'));
            }
            

        //---------------------------------------------------------
        // REMOVE ACCENT
        //---------------------------------------------------------
        
            public function removeAccents(string $str, string $charset = 'utf-8'): string{
                $str = htmlentities($str, ENT_NOQUOTES, $charset);

                $str = preg_replace(
                    '#&([A-Za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#',
                    '$1',
                    $str
                );

                $str = preg_replace('#&([A-Za-z]{2})(?:lig);#', '$1', $str);
                $str = preg_replace('#&[^;]+;#', '', $str);

                return $str ?? '';
            }        

        //---------------------------------------------------------
        // CLEAN URL
        //---------------------------------------------------------

            public function cleanUrl(string $text): string{
                $text = preg_replace('~[^\pL\d]+~u', '-', $text) ?? '';
                $text = trim($text, '-');

                if (function_exists('iconv')) {
                    $converted = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
                    if ($converted !== false) {
                        $text = $converted;
                    }
                }

                $text = strtolower($text);
                $text = preg_replace('~[^-\w]+~', '', $text) ?? '';

                return $text !== '' ? $text : 'n-a';
            }

        //---------------------------------------------------------
        // CLEAN TAG
        //---------------------------------------------------------

            public function cleanTag(string $text): string{
                $separator = ',';

                $toFind  = 'ÀÁÂÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜàáâãäåæçêëìíîïôöùû';
                $replace = 'aaaaaceeeeiiiiooooouuuuaaaaaaaceeiiiioouu';

                $text = strtr(strtolower($text), $toFind, $replace);
                $text = preg_replace('/[^a-z0-9.]/', $separator, $text) ?? '';

                while (str_contains($text, $separator . $separator)) {
                    $text = str_replace($separator . $separator, $separator, $text);
                }

                $text = ltrim($text, $separator);
                $text = rtrim($text, $separator);

                return $text;
            }

        //---------------------------------------------------------
        // VERIF TAG
        //---------------------------------------------------------

            public function verifyTag(string $tag): ?int{

                $req = "SELECT tag FROM ".$this->opts['table_tags']." WHERE tag = :tag";
                $res = $this->pdo->prepare($req);
                $res->bindValue(':tag', $tag, PDO::PARAM_STR);
                $res->execute();
                $tab= $res->fetchAll();
                $nb=count($tab);

                return $nb?:0;
            }

        //---------------------------------------------------------
        // CESURE
        //---------------------------------------------------------

            public function cleanCut(string $string, int $length, string $cutString = '...'): string{
                if (mb_strlen($string) <= $length) {
                    return $string;
                }

                $str = mb_substr($string, 0, $length - mb_strlen($cutString) + 1);
                return mb_substr($str, 0, mb_strrpos($str, ' ')) . $cutString;
            }


        //---------------------------------------------------------
        // SECURE STRING INPUT
        //---------------------------------------------------------

            public function htmlTotxt(?string $string): string{
                $string ??= '';

                $search = [
                    '@<script[^>]*?>.*?</script>@si',
                    '@<[\/\!]*?[^<>]*?>@si',
                    '@<style[^>]*?>.*?</style>@siU',
                    '@<![\s\S]*?--[ \t\n\r]*>@'
                ];

                $text = preg_replace($search, '', $string) ?? '';

                return mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
            }


        //---------------------------------------------------------
        // REMOVE BODY TAG
        //---------------------------------------------------------

            public function removeBody(string $html): string{
                return preg_replace('/<\/?body[^>]*>/i', '', $html) ?? '';
            }

        //---------------------------------------------------------
        // VERIF VALUE TYPE
        //---------------------------------------------------------

            public function setInt($value): ?int{
                $value = (int)$value;
                return (int) ($value ?? 0);
            }

            public function setString($string): ?string{
                $string = (string)$string;
                return (string) ($value ?? '');
            }

             public function setArray($array): ?array{
                if(is_array($array)) {return $array;}
                if($array===null) {return [];}
                if (is_string($array)) {
                    $decoded = json_decode($array, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {return $decoded;}
                }
                return [$array]; 
            }

        //---------------------------------------------------------
        // COLOR FUNCTION
        //---------------------------------------------------------

            public function hexToRgb(string $hex, bool $alpha = false): ?array{
                $hex      = str_replace('#', '', $hex);
                $length   = strlen($hex);
                $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
                $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
                $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
                if ( $alpha ) {
                    $rgb['a'] = $alpha;
                }
                return $rgb?:[];
            }
            
            public function beliefmedia_rgb_to_hsl(string $r, string $g,  string$b): ?array{

                $oldR = $r;
                $oldG = $g;
                $oldB = $b;

                $r /= 255;
                $g /= 255;
                $b /= 255;

                $max = max( $r, $g, $b );
                $min = min( $r, $g, $b );

                $h=""; $s="";
                $l = ( $max + $min ) / 2;
                $d = $max - $min;

                    if( $d == 0 ) {
                    $h = $s = 0;

                    } else {

                    $s = $d / ( 1 - abs( 2 * $l - 1 ) );

                    switch( $max ) {
                        case $r:
                        $h = 60 * fmod( ( ( $g - $b ) / $d ), 6 ); 
                        if ($b > $g) {
                        $h += 360;
                        }
                        break;

                        case $g: 
                        $h = 60 * ( ( $b - $r ) / $d + 2 ); 
                        break;

                        case $b: 
                        $h = 60 * ( ( $r - $g ) / $d + 4 ); 
                        break;
                        }			        	        
                    }
                return array('h' => round($h, 2), 's' => round($s, 2), 'l' => round($l, 2))?:[];
            }
            
            
            public function hslToRgb(string $h, string $s, string $l ): ?array{
                $r=""; 
                $g=""; 
                $b="";
                $c = ( 1 - abs( 2 * $l - 1 ) ) * $s;
                $x = $c * ( 1 - abs( fmod( ( $h / 60 ), 2 ) - 1 ) );
                $m = $l - ( $c / 2 );
                if ( $h < 60 ) {
                    $r = $c;
                    $g = $x;
                    $b = 0;
                } else if ( $h < 120 ) {
                    $r = $x;
                    $g = $c;
                    $b = 0;			
                } else if ( $h < 180 ) {
                    $r = 0;
                    $g = $c;
                    $b = $x;					
                } else if ( $h < 240 ) {
                    $r = 0;
                    $g = $x;
                    $b = $c;
                } else if ( $h < 300 ) {
                    $r = $x;
                    $g = 0;
                    $b = $c;
                } else {
                    $r = $c;
                    $g = 0;
                    $b = $x;
                }
                $r = ( $r + $m ) * 255;
                $g = ( $g + $m ) * 255;
                $b = ( $b + $m  ) * 255;

                return array( floor( $r ), floor( $g ), floor( $b ) )?:[];
            }


    }