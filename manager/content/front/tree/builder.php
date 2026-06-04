<?php

/*
 *  @author Luma Prod - Pierre Cosmao Dumanoir
 *  @copyright  2003-2026 | Luma Prod - Pierre Cosmao Dumanoir
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
   <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
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
  <script src="/plugins/grapesjs/addOns/grapesjs-ia/grapesjs-ia-texte.js"></script>
  <script src="/plugins/cKadmin/ckfinder.js"></script>
  <!-- /JS -->

  <script type="text/javascript">
    var LandingPage_<?= ${$idLangPage};?> = "";
    var editor_<?= ${$idLangPage};?> = grapesjs.init({
      type: 'simpleStorage',
      container: '#gjs_<?= ${$idLangPage};?>',
      clearOnRender: true,
      height: '84.1vh',
      avoidInlineStyle: true,
      telemetry: false,
      fromElement: true,
      showOffsets: 1,
      protectedCss: '',
      //domComponents: { storeWrapper: 0 },
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
            urlLoad: '<?= AJAXPATH; ?>pages/process-builder-load.php',
            fetchOptions: {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/json',
                  'X-Requested-With': 'XMLHttpRequest',
                  'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
              },
              body: JSON.stringify({
                idCategory: '<?= $idCategory; ?>',
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
        clearProperties: 1,
      },
      canvas: {
        styles: [
          '/css/fonts.css',
          '/css/type_builder.css',
          '/css/form.css',
        ]
      },
      assetManager: {
      custom: {
        open(props) {
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
                        /*editor.getSelected(file.getUrl());*/
                        // opts.target.set('src', file.getUrl());
                        // opts.onSelect(file.getUrl());
			                  props.close();			 
                     });
                     finder.on( 'file:choose:resizedImage', function( evt ) {
						            opts.target.set('src', evt.data.resizedUrl);
						            opts.onSelect(evt.data.resizedUrl);
                     });
                 }
             } );
        },
        close(props) {
          // Close the external Asset Manager
        },
      },
    },
      deviceManager: {
        devices: [
            {
                name: 'Desktop',
                width: '',
            },
            {
                name: 'Mobile landscape',
                width: '1024px',
            },
            {
                name: 'Tablet',
                width: '768px',
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
        'grapesjs-rte-extensions',
        'GrapesJsIaTexte'
      ],
      pluginsOpts: {
        'gjs-blocks-basic': { flexGrid: true },
        'grapesjs-preset-webpage': {
          modalImportTitle: 'Importez un Templates',
          modalImportLabel: '<div style="margin-bottom: 10px; font-size: 13px;">Coller votre code HTML/CSS et cliquez sur Import</div>',
          modalImportContent: function(editor_<?= ${$idLangPage};?>) {
            return editor_<?= ${$idLangPage};?>.getHtml() + '<style>' + editor_<?= ${$idLangPage};?>.getCss() + '</style>'
          },
          aviaryOpts: false,
          blocksBasicOpts: { flexGrid: 1 },
          navbarOpts: {},
        },
         GrapesJsIaTexte: {
          apiUrl: '<?= AJAXPATH; ?>agent/process-ia-text.php',
          buttonLabel: '<svg viewBox="0 0 24 24"><path d="M18.95.22c-.61,1.61-2.01,3.38-3.8,3.8,1.79.42,3.19,2.19,3.8,3.8.61-1.61,2.01-3.38,3.8-3.8-1.79-.42-3.19-2.19-3.8-3.8Z"/><g><path d="M14.56,16.01h-1.98l-.79-2.04h-3.6l-.74,2.04h-1.93l3.5-9h1.92l3.6,9ZM11.22,12.45l-1.24-3.34-1.22,3.34h2.45Z"/><path d="M15.51,8.61v-1.6h1.72v1.6h-1.72ZM15.51,16.01v-6.52h1.72v6.52h-1.72Z"/></g><path d="M16.54,21.41H6.23c-2.37,0-4.29-1.93-4.29-4.29V6.81c0-2.37,1.93-4.29,4.29-4.29h7.52c.61,0,1.11.5,1.11,1.11s-.5,1.11-1.11,1.11h-7.52c-1.14,0-2.07.93-2.07,2.07v10.31c0,1.14.93,2.07,2.07,2.07h10.31c1.14,0,2.07-.93,2.07-2.07v-7.41c0-.61.5-1.11,1.11-1.11s1.11.5,1.11,1.11v7.41c0,2.37-1.93,4.29-4.29,4.29Z"/></svg>',
        }
      }
      
    });

    //PASTE METHOD
    editor_<?= ${$idLangPage};?>.on('rte:enable', (rte, component) => {
        const el = rte.el;

        el.addEventListener('paste', e => {
            e.preventDefault();

            const text = (e.clipboardData || window.clipboardData).getData('text/plain');

            if (rte && typeof rte.insertHTML === 'function') {
                rte.insertHTML(text);
            } else {
                document.execCommand('insertText', false, text);
            }
        });
    });


    // AJOUT POLICES
    const prop_<?= ${$idLangPage};?> = editor_<?= ${$idLangPage};?>.StyleManager.getProperty('typography', 'font-family');
	  prop_<?= ${$idLangPage};?>.set('options', [
      {value: "AvenirNextLTPro-Regular", name: 'Avenir Next Regular'},
      {value: "AvenirNextLTPro-Demi", name: 'Avenir Next Demi'},
      {value: "AvenirNextLTPro-Italic", name: 'Avenir Next Italic'},
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

    ['display', 'flex-direction', 'justify-content', 'align-items', 'flex-wrap'].forEach(prop => {
      sm_<?= ${$idLangPage};?>.removeProperty('flex', prop);
    });

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

  // SAUVEGARDES
  editor_<?= ${$idLangPage};?>.Commands.add('save-db', {
      run(editor, sender) {
        sender && sender.set('active', 0);

        const html = editor.getHtml();
        const css = editor.getCss();
        const components = editor.getComponents();
        const styles = editor.getStyle();

        const payload = {
          id: <?= $idCategory; ?>,
          idLang: <?= ${$idLangPage}; ?>,
            html,
            css,
            components,
            styles,
          };

        ajax.post('<?= AJAXPATH; ?>pages/process-builder-save.php', {
            id: <?= $idCategory; ?>,
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