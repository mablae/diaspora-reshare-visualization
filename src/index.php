<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->


    <link rel="stylesheet" href="css/normalize.css">


    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-responsive.min.css">
    <link rel="stylesheet" href="css/main.css?v=0.0.1">
    <script src="js/vendor/modernizr-2.6.2.min.js"></script>
</head>
<body>
<!--[if lt IE 7]>
<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
    your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to
    improve your experience.</p>
<![endif]-->

<!-- Add your site or application content here -->
<a class="githubForkMeBagde" href="https://github.com/mablae/diaspora-reshare-visualization"><img src="https://s3.amazonaws.com/github/ribbons/forkme_right_red_aa0000.png" alt="Fork me on GitHub"></a>
<div id="topNav">
        <div  class="container">
            <div class="row">
                <div class="span6"><h4>Diaspora reShareViewer</h4>
                    <p>A simple D3.js powered graph to follow your posts.<br>
                       (<strong>Bookmarklet</strong> for your convenience: <a href="javascript:window.location.href = 'http://mablae.taurus.uberspace.de/diaspora_vis/?startUrl='+encodeURIComponent(window.location.href+'.json');void 0;">Diaspora ReShare Viewer</a>
                        )</p></div>
                <div class="span6"><form class="form-horizontal" id="searchForm">
                    <label for="startUrl" class="">Please enter the URL for your Diaspora Post</label>
                    <input type="text" placeholder="https://pod.geraspora.de/posts/965127.json" class="span4" id="startUrl" name="startUrl" value="<?php echo !empty($_GET['startUrl']) ? $_GET['startUrl'] : '' ?>"/>
                    <button type="submit" class="btn">Start</button>
                </form>   <div id="loader"><img src="img/loader.gif"/></div></div>
            </div>
        </div>





</div>


<div id="mainBox">




</div>


<script src="js/jquery-1.8.3.min.js"></script>
<script src="js/d3.v3.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js?v=0.0.1"></script>

<?php

if (!empty($_GET['startUrl'])) {
?>

    <script>
        $(function() {
            $('#searchForm').submit();
        });
    </script>

<?php
}
?>

</body>
</html>
