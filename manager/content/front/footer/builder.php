<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2025 | Luma Prod - Pierre Cosmao Dumanoir
 *  @version  Release: 3
 */

	//---------------------------------------------------------  
	// FILE SECURE
	//---------------------------------------------------------  

		if (!$session->getAdminData()) {
			header('HTTP/1.1 403 Forbidden');
			exit('Accès interdit');
		}


	//---------------------------------------------------------  
	// INIT CKFINDER
	//---------------------------------------------------------  

		$_SESSION['DATA_BASE_URL']='/tmp/data/Base'.strtoupper($varLink).'/';
		$_SESSION['DATA_BASE_STATUS']=0;
?>

  <!-- CSS -->
  <link rel="stylesheet" href="/plugins/grapesjs/dist/css/grapes.min.css"  />
  <link rel="stylesheet" href="/plugins/grapesjs/dist/css/index.css"  />
  <link rel="stylesheet" href="/plugins/grapesjs/addOns/grapesjs-rte-extensions/grapesjs-rte-extensions.min.css">
  <!-- /CSS -->


  <div id="builderMsg_<?= ${$idLangPage};?>">&nbsp;</div>
  <div id="gjs_<?= ${$idLangPage};?>"></div>

  <!-- JS -->
  <script src="/plugins/grapesjs/dist/grapes.min.js"></script>
  <script src="/plugins/grapesjs/locate/test.js"></script>
  <script src="/plugins/grapesjs/addOns/grapesjs-preset-webpage/index.js"></script>
  <script src="/plugins/grapesjs/addOns/grapesjs-blocks-basic/index.js"></script>
  <script src="/plugins/grapesjs/addOns/grapesjs-plugin-forms/index.js"></script>
  <script src="/plugins/grapesjs/addOns/grapesjs-plugin-export/grapesjs-plugin-export.min.js"></script>
  <script src="/plugins/grapesjs/addOns/grapesjs-custom-code/index.js"></script>
  <script src="/plugins/grapesjs/addOns/grapesjs-style-bg/index.js"></script>
  <script src="/plugins/grapesjs/addOns/grapesjs-plugin-header/index.js"></script>
  <script src="/plugins/grapesjs/addOns/grapesjs-custom-code/index.js"></script>
  <script src="/plugins/grapesjs/addOns/grapesjs-rte-extensions/grapesjs-rte-extensions.min.js"></script>
  <script src="/plugins/cKadmin/ckfinder.js"></script>
  <!-- /JS -->


  <script type="text/javascript">
    var LandingPage_<?= ${$idLangPage};?> = "";
    var editor_<?= ${$idLangPage};?> = grapesjs.init({
      type: 'simpleStorage',
      container: '#gjs_<?= ${$idLangPage};?>',
      clearOnRender: true,
      height: '88vh',
      avoidInlineStyle: 1,
      fromElement: false,
      telemetry: false,
      showOffsets: 1,
      protectedCss: '',
      components: LandingPage_<?= ${$idLangPage};?>.components || LandingPage_<?= ${$idLangPage};?>.html,
	    style: LandingPage_<?= ${$idLangPage};?>.style || LandingPage_<?= ${$idLangPage};?>.css,
      storageManager: {
        type: 'remote',
        autosave: false,
        autoload: true,
        storeComponents: true,
        storeStyles: true,
        storeHtml: true,
        storeCss: true,
        contentTypeJson: true,
        options: {
          remote: {
            urlLoad: '<?= AJAXPATH; ?>pages/process-footer-load.php',
            
            fetchOptions: {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/json',
                  'X-Requested-With': 'XMLHttpRequest',
                  'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
              },
              body: JSON.stringify({
                idLang: '<?= ${$idLangPage}; ?>'
              })
            },

            onLoad: result => result.data,
          }
        }
      },
      selectorManager: {
        componentFirst: true
      },
      styleManager: {
        clearProperties: 1
      },
      canvas: {
        styles: [
          '/css/fonts.css',
          '/css/type_builder.css',
        ]
      },
      assetManager: {
        custom: {
          open(props) {
            CKFinder.popup({
              chooseFiles: true,
              rememberLastFolder: false,
              resourceType: 'IMG',
			        startupPath: 'IMG:/',
              onInit: function(finder) {
                finder.on('files:choose', function(evt) {
                  var file = evt.data.files.first();
                  editor_<?= ${$idLangPage};?>.getSelected().set({ src: file.getUrl() });
                  editor_<?= ${$idLangPage};?>.getSelected().addStyle({ 'background-image': 'url('+file.getUrl()+')' });
                  props.close();
                });
              }
            });
          },
          close(props) {},
        },
      },
      deviceManager: {
        devices: [
            {
                name: 'Desktop',
                width: '',
            },
            {
                name: 'Tablet',
                width: '768px',
            },
            {
                name: 'Mobile landscape',
                width: '1024px',
            },
            {
                name: 'Mobile portrait',
                width: '480px',
            }
          ]
      },
      i18n: {
            messages: {
                fr: window.translations
            },
            locale: 'fr' 
      },
      plugins: [
        'gjs-blocks-basic',
        'grapesjs-plugin-forms',
        'grapesjs-plugin-export',
        'grapesjs-custom-code',
        'grapesjs-style-bg',
        'grapesjs-plugin-header',
        'grapesjs-preset-webpage',
        'grapesjs-rte-extensions'
      ],
      pluginsOpts: {
        'gjs-blocks-basic': { flexGrid: true },
        'grapesjs-rte-extensions': {
          base: {
            bold: true,
            italic: true,
            underline: true,
            strikethrough: true,
            link: true,
          },
          fonts: {
			      fontSize: true,
            fontColor: true,
            hilite: true,
          },
          format: {
            heading1: true,
            heading2: true,
            heading3: true,
            paragraph: true,
            clearFormatting: true,
          },
          subscriptSuperscript: false,
          indentOutdent: false,
          list: true,
          align: true,
          actions: false,
          undoredo: false,
          extra: false,
          darkColorPicker: true,
          maxWidth: '900px'
        },
        'grapesjs-preset-webpage': {
          modalImportTitle: 'Importez un Templates',
          modalImportLabel: '<div style="margin-bottom: 10px; font-size: 13px;">Coller votre code HTML/CSS et cliquez sur Import</div>',
          modalImportContent: function(editor_<?= ${$idLangPage};?>) {
            return editor_<?= ${$idLangPage};?>.getHtml() + '<style>' + editor_<?= ${$idLangPage};?>.getCss() + '</style>'
          },
          aviaryOpts: false,
          blocksBasicOpts: { flexGrid: 1 },
          navbarOpts: false,
        }
      }
      
    });


    // AJOUT POLICES
    const prop_<?= ${$idLangPage};?> = editor_<?= ${$idLangPage};?>.StyleManager.getProperty('typography', 'font-family');
	  prop_<?= ${$idLangPage};?>.set('options', [
      {value: "AvenirNextLTPro-Regular", name: 'Avenir Next Regular'},
      {value: "AvenirNextLTPro-Demi", name: 'Avenir Next Demi'},
      {value: "Times New Roman", name: 'Times New Roman'},
      {value: "Arial Black", name: 'Arial Black'},
      {value: "Tahoma", name: 'Tahoma'},
      {value: "Verdana, Geneva, sans-serif", name: 'Verdana'},
      {value: "sans-serif", name: 'sans-serif'},
      {value: "Open Sans', sans-serif", name: 'Open Sans'},
      {value: "Helvetica Neue,Helvetica,Arial,sans-serif", name: 'Helvetica'},
	  ]);
	

    const propFontSize_<?= ${$idLangPage};?> = editor_<?= ${$idLangPage};?>.StyleManager.getProperty('typography', 'font-size');
    propFontSize_<?= ${$idLangPage};?>.set('units', ['px', 'em', 'rem', '%', 'vw']);


    // Ajoute text-decoration au Style Manager
    editor_<?= ${$idLangPage};?>.StyleManager.addProperty('typography', {
      property: 'text-decoration',
      name: 'Décoration texte',
      type: 'radio',
      defaults: 'none',
      options: [
        { value: 'none', name: '<span title="Aucune">✖</span>' },
        { value: 'underline', name: '<i class="fa fa-underline" title="Souligné"></i>' },
        { value: 'line-through', name: '<i class="fa fa-strikethrough" title="Barré"></i>' },
        { value: 'overline', name: '<span style="text-decoration: overline;" title="Ligne haute">T</span>' }
      ]
    });


    const sm_<?= ${$idLangPage};?> = editor_<?= ${$idLangPage};?>.StyleManager;

    const textAlignProp_<?= ${$idLangPage};?> = sm_<?= ${$idLangPage};?>.getProperty('typography', 'text-align');

    if (textAlignProp_<?= ${$idLangPage};?>) {
      textAlignProp_<?= ${$idLangPage};?>.set('options', [
        { value: 'left', name: '<i class="fa fa-align-left" title="Aligné à gauche"></i>' },
        { value: 'center', name: '<i class="fa fa-align-center" title="Centré"></i>' },
        { value: 'right', name: '<i class="fa fa-align-right" title="Aligné à droite"></i>' },
        { value: 'justify', name: '<i class="fa fa-align-justify" title="Justifié"></i>' }
      ]);
    }


    // Supprime d’abord d’anciennes définitions si elles existent
    ['display', 'flex-direction', 'justify-content', 'align-items', 'flex-wrap'].forEach(prop => {
      sm_<?= ${$idLangPage};?>.removeProperty('flex', prop);
    });

    // ✅ 1. Activer le mode Flex
    sm_<?= ${$idLangPage};?>.addProperty('flex', {
      property: 'display',
      name: 'Mode flex',
      type: 'radio',
      defaults: 'flex',
      options: [
        { value: 'block', name: '<i class="fa fa-square" title="Bloc"></i>' },
        { value: 'flex', name: '<i class="fa fa-bars" title="Flexbox"></i>' }
      ]
    });

    // ✅ 2. Direction du flux (flex-direction)
    sm_<?= ${$idLangPage};?>.addProperty('flex', {
      property: 'flex-direction',
      name: 'Direction',
      type: 'radio',
      defaults: 'row',
      options: [
        { value: 'row', name: '<i class="fa fa-long-arrow-right" title="Ligne →"></i>' },
        { value: 'row-reverse', name: '<i class="fa fa-long-arrow-left" title="Ligne ←"></i>' },
        { value: 'column', name: '<i class="fa fa-long-arrow-down" title="Colonne ↓"></i>' },
        { value: 'column-reverse', name: '<i class="fa fa-long-arrow-up" title="Colonne ↑"></i>' }
      ]
    });

    // ✅ 3. Justification horizontale (justify-content)
    sm_<?= ${$idLangPage};?>.addProperty('flex', {
      property: 'justify-content',
      name: 'Justifier',
      type: 'radio',
      defaults: 'flex-start',
      options: [
        { value: 'flex-start', name: '<i class="fa fa-align-left" title="Début"></i>' },
        { value: 'center', name: '<i class="fa fa-align-center" title="Centre"></i>' },
        { value: 'flex-end', name: '<i class="fa fa-align-right" title="Fin"></i>' },
        { value: 'space-between', name: '<i class="fa fa-arrows-h" title="Espacé"></i>' },
        { value: 'space-around', name: '<i class="fa fa-ellipsis-h" title="Autour"></i>' }
      ]
    });

    // ✅ 4. Alignement vertical (align-items)
    sm_<?= ${$idLangPage};?>.addProperty('flex', {
      property: 'align-items',
      name: 'Alignement',
      type: 'radio',
      defaults: 'stretch',
      options: [
        { value: 'flex-start', name: '<i class="fa fa-arrow-up" title="Haut"></i>' },
        { value: 'center', name: '<i class="fa fa-arrows-v" title="Centre"></i>' },
        { value: 'flex-end', name: '<i class="fa fa-arrow-down" title="Bas"></i>' },
        { value: 'stretch', name: '<i class="fa fa-expand" title="Étiré"></i>' }
      ]
    });

    // ✅ 5. Enroulement (flex-wrap)
    sm_<?= ${$idLangPage};?>.addProperty('flex', {
      property: 'flex-wrap',
      name: 'Retour ligne',
      type: 'radio',
      defaults: 'nowrap',
      options: [
        { value: 'nowrap', name: '<i class="fa fa-minus" title="Une ligne"></i>' },
        { value: 'wrap', name: '<i class="fa fa-retweet" title="Retour ligne"></i>' },
        { value: 'wrap-reverse', name: '<i class="fa fa-exchange" title="Retour inversé"></i>' }
      ]
    });



  // ASSET
  editor_<?= ${$idLangPage};?>.Commands.add('open-assets', {
		run(editor, sender, opts = {}) {
				CKFinder.popup( {
					chooseFiles: true,
					rememberLastFolder:false,
          resourceType: 'IMG',
			    startupPath: 'IMG:/',
          onInit: function( finder ) {
            finder.on( 'files:choose', function( evt ) {
              var file = evt.data.files.first();
              editor_<?= ${$idLangPage};?>.getSelected().set({ src: file.getUrl() });
              editor_<?= ${$idLangPage};?>.getSelected().addStyle({ 'background-image': 'url('+file.getUrl()+')' });
            });
            finder.on( 'file:choose:resizedImage', function( evt ) {
              opts.target.set('src', evt.data.resizedUrl);
              opts.onSelect(evt.data.resizedUrl);
            });
          }
        });
			}
	});


  editor_<?= ${$idLangPage};?>.Commands.add('save-db', {
    run(editor, sender) {
        sender && sender.set('active', 0);

        ajax.post('<?= AJAXPATH; ?>pages/process-footer-save.php', {
            idLang: <?= ${$idLangPage}; ?>,
            html: editor_<?= ${$idLangPage};?>.getHtml(),
            css: editor_<?= ${$idLangPage};?>.getCss(),
            components: editor_<?= ${$idLangPage};?>.getComponents(),
            styles: editor_<?= ${$idLangPage};?>.getStyle(),
        })
        .then(data => {
            showWarningMessage("builderMsg_<?= ${$idLangPage};?>", "Modifications enregistrées", "#81B929");
        })
        .catch(err => {
            showWarningMessage("builderMsg_<?= ${$idLangPage};?>", "Erreur de sauvegarde", "#dd0202");
        });
      }
  });

    editor_<?= ${$idLangPage};?>.Panels.addButton('options', [{
      id: 'save-db',
      className: 'fa fa-save',
      command: 'save-db',
      attributes: { title: 'Sauvegarder' },
      label: '',
    }]);

      editor_<?= ${$idLangPage};?>.Panels.getButton('options', 'sw-visibility').set('active', 1);
      editor_<?= ${$idLangPage};?>.getConfig().showDevices = 0;

  </script>