<?php
/*
https://github.com/donShakespeare/
http://www.leofec.com/modx-revolution/
(c) 2016 by donShakespeare: MODX & Content Editor Specialist
Deo Gratias!
*/
require_once dirname(dirname(dirname(__FILE__))).'/qa-include/qa-base.php';
require_once QA_INCLUDE_DIR.'qa-app-users.php';

if (qa_get_logged_in_userid() === null){
    die('<div style="position: fixed; margin: auto;width: 400px;height:200px;text-align:center;top:0;bottom:0;left:0;right:0;"><h1>IT SEEMS YOU DO NOT HAVE PERMISSION TO USE THIS COOL MANAGER</h1></div>');
}

function TinymceWrapperGetUrlParam($namee, $inte, $maxe, $defaulte){
    $name = $namee ?: 'p';
    $int = $inte ?: false;
    $max = $maxe ?:20;
    $output = $defaulte;

    // get the sanitized value if there is one
    if (isset($_GET[$name])) {
        if ($int) {
            $value = intval($_GET[$name]);
        } else {
            if (strlen($_GET[$name]) > $max) {
                $value = filter_var(substr($_GET[$name],0,$max), FILTER_SANITIZE_STRING);
            } else {
                $value = filter_var($_GET[$name], FILTER_SANITIZE_STRING);
            }
        }

        $output = rawurldecode($value);
            if($name == 'onlyMimes'){
            $output = '["'.$output.'"]';
        }
            if($name == 'user'){
            $output = '?name='.$output;
        }
    }
    echo $output;
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>elFinder</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2" />
    <!-- jQuery and jQuery UI (REQUIRED) -->
    <link rel="stylesheet" type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <!-- elFinder CSS (REQUIRED) -->
    <link rel="stylesheet" type="text/css" href="elfinder/css/elfinder.min.css">
    <link rel="stylesheet" type="text/css" href="elfinder/css/theme.css">
    <link rel="stylesheet" type="text/css" media="screen" href="elfinderthemes/<?php TinymceWrapperGetUrlParam('theme', '', 10, 'windows-10') ?>/css/theme.css">
    
    <!-- elFinder JS (REQUIRED) -->
    <script src="elfinder/js/elfinder.min.js"></script>
    <!-- elFinder translation (OPTIONAL)
    <script src="js/i18n/elfinder.ru.js"></script>-->
    <!-- elFinder initialization (REQUIRED) -->
    <script type="text/javascript" charset="utf-8">
    var FileBrowserDialogue = {
        init: function() {
        // Here goes your code for setting your custom things onLoad.
        },
        mySubmit: function (Url) {
            // pass selected file path to TinyMCE
            parent.tinymce.activeEditor.windowManager.getParams().oninsert(Url);
            // close popup window
            parent.tinymce.activeEditor.windowManager.close();
        }
    }
    // the official getQueryVariable is no longer reliable ... use MODX/PHP instead
    /*function getQueryVariable(variable){
        var query = window.location.search.substring(1);
        var vars = query.split("&");
        for (var i=0;i<vars.length;i++) {
            var pair = vars[i].split("=");
            if(pair[0] == variable){return pair[1];}
        }
        return(false);
    }*/

    // Documentation for client options:
    // https://github.com/Studio-42/elFinder/wiki/Client-configuration-options
    $(document).ready(function() {
        $('#elfinder').elfinder({
            // TinymceWrapperGetUrlParam(namee, inte, maxe, defaulte)
            onlyMimes: <?php TinymceWrapperGetUrlParam('onlyMimes','','', '[]') ?>, // see docs ...
            validName: '/^[^un]$/',
            defaultView: '<?php TinymceWrapperGetUrlParam('defaultView','','', 'icons') ?>', // or list
            sort: '<?php TinymceWrapperGetUrlParam('sort','','', 'nameDirsFirst') ?>',
            /* nameDirsFirst - sort by name, directory first
            kindDirsFirst - sort by kind, name, directory first
            sizeDirsFirst - sort by size, name, directory first
            name - sort by name
            kind - sort by kind, name
            size - sort by size, name
            */
            sortDirect: '<?php TinymceWrapperGetUrlParam('sortDirect','','', 'asc') ?>', // or desc
            commands : [
             'reload', 'home', 'up', 'back', 'forward', 'getfile', 'quicklook',
            'download', 'rm', 'duplicate', 'rename', 'mkdir', 'mkfile', 'upload', 'copy',
            'cut', 'paste', 'edit', 'extract', 'archive', 'search', 'info', 'view', 'help',
            'resize', 'sort'
            ],
            getFileCallback: function(file) { // editor callback
                // file.url - commandsOptions.getfile.onlyURL = false (default)
                // file     - commandsOptions.getfile.onlyURL = true
                FileBrowserDialogue.mySubmit(file.url); // pass selected file path to TinyMCE
            },

            ui:['<?php TinymceWrapperGetUrlParam('toolbar', 1, 1, 'toolbar') ?>','<?php TinymceWrapperGetUrlParam('places', 1, 1, 'places') ?>', '<?php TinymceWrapperGetUrlParam('tree', 1, 1, 'tree') ?>', '<?php TinymceWrapperGetUrlParam('path', 1, 1, 'path') ?>', '<?php TinymceWrapperGetUrlParam('stat', 1, 1, 'stat') ?>'],

            uiOptions : {
                // toolbar configuration
                toolbar : [
                    ['back', 'forward'],
                    //['reload'],
                    // ['home', 'up'], //not supported in Windows-10 theme
                    ['up'],
                    ['mkdir', 'mkfile', 'upload'],
                    ['open', 'download', 'getfile'],
                    ['info'],
                    ['quicklook'],
                    ['copy', 'cut', 'paste'],
                    ['rm'],
                    ['duplicate', 'rename', 'edit', 'resize'],
                    ['extract', 'archive'],
                    ['search'],
                    ['view'],
                    ['help']
                ],

                // directories tree options
                tree : {
                    // expand current root on init
                    openRootOnLoad : true,
                    // auto load current dir parents
                    syncTree : true
                },

                // navbar options
                navbar : {
                    minWidth : 250,
                    maxWidth : 500
                },

                // current working directory options
                cwd : {
                    // display parent directory in listing as ".."
                    oldSchool : true
                }
            },

            placesFirst:true,

            dateFormat: 'M d, Y h:i A',

            disableShortcuts: false,

            contextmenu : {
                // navbarfolder menu
                navbar : ['open', '|', 'copy', 'cut', 'paste', 'duplicate', '|', 'rm', '|', 'info'],

                // current directory menu
                cwd    : ['reload', 'back', '|', 'upload', 'mkdir', 'mkfile', 'paste', '|', 'info'],

                // current directory file menu
                files  : [
        'getfile', '|','open', 'quicklook', '|', 'download', '|', 'copy', 'cut', 'paste', 'duplicate', '|',
        'rm', '|', 'edit', 'rename', 'resize', '|', 'archive', 'extract', '|', 'info'
                ]
            },
            resizable: false,

            rememberLastDir : <?php TinymceWrapperGetUrlParam('rememberLastDir', 1, 1, "false") ?>,

            useBrowserHistory : <?php TinymceWrapperGetUrlParam('useBrowserHistory', 1, 1, "false") ?>,

            customData : { //comming soon (only useful if using MODX)
                // unlocked: "<?php TinymceWrapperGetUrlParam('unlocked', 1, 1, "0") ?>", //option is '1'
                folder: "<?php TinymceWrapperGetUrlParam('folder', '', 50, 'null') ?>",
                // hide: "<?php TinymceWrapperGetUrlParam('hide', '', 5, "false") ?>", // options 1/2/3/4/p (e.g .com?hide=1234p OR e.g .com?hide=14p)
                // pset: "<?php TinymceWrapperGetUrlParam('pset', '', 20, 'Default') ?>" //(e.g .com?pset=myCustomSet)
            },

            url : 'qa-elfinder-connector.php'  // connector URL (REQUIRED - DO NOT TOUCH) -- you may use your own connector
            // , lang: 'ru'                    // language (OPTIONAL)
        });
    });
    $(window).resize(function(){
        $("#elfinder").css("height", $(window).height()*0.97);
    })
    </script>
  </head>
  <body style="overflow:hidden; background-color:#fff;">
    <div id="elfinder"></div>
  </body>
</html>