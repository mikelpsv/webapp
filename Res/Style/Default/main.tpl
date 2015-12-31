<html>
<head>
    <title>{{echo $title}}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="/Res/img/favicon.ico" rel="shortcut icon">
    <?php if (isset($bootstrap)) : ?>
        <link rel="stylesheet" href="/Res/bootstrap_<?php echo $bootstrap; ?>/css/bootstrap.css"/>
        <script type="text/javascript" src="/Res/bootstrap_<?php echo $bootstrap; ?>/jquery.min.js"></script>
        <script type="text/javascript" src="/Res/bootstrap_<?php echo $bootstrap; ?>/js/bootstrap.min.js"></script>
    <?php endif ?>
    <link rel="stylesheet" href="/Res/font_awesome/css/font-awesome.min.css">

    <link rel="stylesheet" href="/Res/js/jquery.toast.min.css">
    <script type="text/javascript" src="/Res/js/jquery.toast.min.js"></script>

    <script type="text/javascript" src="/Res/js/scrolltofixed/jquery-scrolltofixed-min.js"></script>

    <script type="text/javascript" src="/Res/js/is.min.js"></script>

    <link rel="stylesheet" href="/Res/js/horizontal-scrollbars/jquery.horizontal.scroll.css">
    <script type="text/javascript" src="/Res/js/horizontal-scrollbars/jquery.horizontal.scroll.js"></script>

    <link rel="stylesheet" href="/Res/js/jtable/themes/metro/lightgray/jtable.min.css">
    <script type="text/javascript" src="/Res/js/jquery-ui/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/Res/js/jtable/jquery.jtable.js"></script>
    <script type="text/javascript" src="/Res/js/jtable/localization/jquery.jtable.ru.js"></script>

    <script type="text/javascript" src="/Res/js/main.js"></script>

    <link rel="stylesheet" type="text/css" href="/Res/main.css">
    <?php if (isset($bootstrap) && $bootstrap == 2) : ?>
        <link rel="stylesheet" href="/Res/ie_old.css"/>
    <?php endif ?>

    <!--<link rel="stylesheet" type="text/css" href="{{echo TPL_DIR}}/calendar.css"/>-->
    <link rel="stylesheet" type="text/css" href="/Lib/modal/subModal.css"/>

    <script type="text/javascript" src="/Lib/modal/common.js"></script>
    <!--<script type="text/javascript" src="/Lib/modal/subModal.js"></script>-->
    <!--<script type="text/javascript" src="/Lib/calendar.js"></script>-->

    <script type="text/javascript">
        function getXmlHttp() {
            if (typeof window.XMLHttpRequest === 'undefined') {
                XMLHttpRequest = function () {
                    try {
                        return new ActiveXObject("Msxml2.XMLHTTP.6.0");
                    }
                    catch (e)
                    {}
                    try {
                        return new ActiveXObject("Msxml2.XMLHTTP.3.0");
                    }
                    catch (e)
                    {}
                    try {
                        return new ActiveXObject("Msxml2.XMLHTTP");
                    }
                    catch (e)
                    {}
                    try {
                        return new ActiveXObject("Microsoft.XMLHTTP");
                    }
                    catch (e)
                    {}
                    throw new Error("This browser does not support XMLHttpRequest.");
                };
            }
            return new window.XMLHttpRequest();
        }

        function addFile() {
            var filelist = document.getElementById('file_list');
            var temp_file = document.getElementById('fileblock');
            var new_file = temp_file.cloneNode(true);
            new_file.style.display = 'block';

            filelist.appendChild(new_file);

        }

        function delFile(item) {
            var filelist = document.getElementById('file_list');
            if (filelist.childNodes.length > 1) {
                filelist.removeChild(item.parentNode.parentNode.parentNode.parentNode);
            }
        }

        {{if(isset($_SESSION['flash'])):}}
            $(function() {
                var flashMessages = $.parseJSON('{{echo json_encode(FlashMessage::getFlashes())}}');
                for (var i = 0; i < flashMessages.length; ++i) {
                    $.toast(flashMessages[i].message, {type: flashMessages[i].class});
                }
            });
        {{endif}}
    </script>

</head>

<body>
    <table id="header">
        <tr style="border:0px;">
            <td valign="bottom">
                <a href="/">
                    <img src="/Res/{{echo Model_Office::$filialLogo ? 'uploaded/' . Model_Office::$filialLogo : 'img/puls.png'}}" style="margin-left:0px;border:0px;" width="300" align="top"/>
                </a>
            </td>
            <td>&nbsp;</td>
            <!-- {{echo $_SESSION['Office'][FilialName]}}-->
            <td style="border:0;width:18%;font-size:12px;"
                valign="top">{{echo $_SESSION['Office']['Client']}}
                {{echo $_SESSION['Office']['ClientManagerName'] ? '<br><span class="icon-user glyphicon glyphicon-user"></span>'.$_SESSION['Office']['ClientManagerName'] : ''}}
                {{echo $_SESSION['Office']['ClientManagerPhone'] ? '<br><span class="icon-comment glyphicon glyphicon-phone-alt"></span>'.$_SESSION['Office']['ClientManagerPhone'] : ''}}
                {{echo $_SESSION['Office']['ClientManagerMail'] ? '<br><span class="icon-envelope glyphicon glyphicon-envelope"></span>'.$_SESSION['Office']['ClientManagerMail'] : ''}}</td>
        </tr>

        <tr id="headline">
            <td>
                {{foreach($main_menu as $menu_item):}}
                {{echo $menu_item}}
                {{endforeach}}
            </td>
            <td>&nbsp;</td>
            <td style="width:200px;" class="exit-cell"><a href="{{echo isset($logoutLink) ? $logoutLink : '/login/logout/'}}" class="exit">Выход</a><i class="fa fa-sign-out fa-inverse"></i></td>
        </tr>
        {{if(isset($context)):}}
        <tr id="contextline">
            <td colspan="3">
                <ul id="leftmenu">
                    {{foreach($context as $menu_item):}}
                    {{if (isset($menu_item['badge']) && $menu_item['badge'] !== false && !is_null($menu_item['badge'])):}}
                    {{$badge = '<span class="badge">'.$menu_item['badge'].'</span>'}}
                    {{else:}}
                    {{$badge = ''}}
                    {{endif}}
                    {{if($menu_item['href'] === ''):}}
                    <li>{{echo $menu_item['caption']}}{{echo $badge}}</li>
                    {{else:}}
                    <li><a href="{{echo $menu_item['href']}}">{{echo $menu_item['caption']}}</a>{{echo $badge}}</li>
                    {{endif}}
                    {{endforeach}}
                </ul>
            </td>
        </tr>
        {{endif}}
    </table>

    <table id="body" width="100%" border="0">
        <tr>
            <td colspan="3" class="h">


            </td>
        </tr>
        <tr>
            <?php /*
            <td class="l" valign="top" align="left" width="225">

                {{if(isset($context)):}}
                <ul id="leftmenu">
                    {{foreach($context as $menu_item):}}
                    {{if($menu_item['href'] === ''):}}
                    <li>{{echo $menu_item['caption']}}</li>
                    {{else:}}
                    <li><a href="{{echo $menu_item['href']}}">{{echo $menu_item['caption']}}</a></li>
                    {{endif}}
                    {{endforeach}}
                </ul>
                {{endif}}
            </td>
            */ ?>
            <td class="b" colspan="3" valign="top">
                {{if(isset($path)):}}
                    <ol class="breadcrumb">
                        {{foreach($path as $k=>$path_item):}}
                            {{echo (($k==0)?'':'<span class="divider">/</span>')}}
                            {{if($path_item['href'] != ''):}}
                                <li><a href="{{echo $path_item['href']}}">{{echo $path_item['caption']}}</a></li>
                            {{else:}}
                                <li class="active">{{echo $path_item['caption']}}</li>
                            {{endif;}}
                        {{endforeach}}
                    </ol>
                {{endif}}


            </td>
        </tr>
    </table>

    <div class="page-body">
        {{echo $body}}
    </div>

    {{if (isset($stat)):}}
    <div class="stat-line">Время выполнения запроса: {{echo number_format($stat, 4, ',', ' ')}}</div>
    {{endif}}
</body>
</html>