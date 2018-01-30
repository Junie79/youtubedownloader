<?php
$response = array(
    'data' => null,
    'error' => null,
);
$isResponse = false;
$isError = false;
$isSuccessResponse = false;
$providerName = null;
$url = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isResponse = true;
    require_once __DIR__ . '/YoutubeDownloader.php';
    try {
        if (!isset($_POST['url']) || !trim($_POST['url'])) {
            throw new VideoDownloaderException("Url does not set");
        }
        $url = trim($_POST['url']);
        $yd = new YoutubeDownloader($url);
        $fullInfo = $yd->getFullInfo();
        $videoId = $fullInfo['video_id'];
        $response['data'] = array(
            'baseInfo' => $yd->getBaseInfo(),
            'downloadInfo' => $yd->getDownloadsInfo(),
        );
        $isSuccessResponse = true;
    } catch (Exception $e) {
        $isError = true;
        header('Bad request', true, 400);
        $response['error'] = $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">
    <link async href="http://fonts.googleapis.com/css?family=Warnes" data-generated="http://enjoycss.com" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" type="image/png" href="https://www.youtube.com/yts/img/favicon-vfl8qSV2F.ico"/>
    <title>#YouTube-Video-Downloader</title>
    <style type="text/css" media="screen">
        /* h1 {
            text-align: center;
            font-family: Comic Sans MS;
            font-weight: bold;
            background: -webkit-linear-gradient(#ff6a6a, #ffbfbf);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
         } */
         p{
  text-transform:uppercase;
  font-size:60px;
  font-family: Comic Sans MS;
  padding:30px;
  text-align: center;
} 
        * {
            font-family: comic sans ms;
        } 
        footer {
            text-align: center;
        }
        #video-name {
            text-align: center;
            color: #ff6a6a;
        }
        #video-preview {
            text-align: right;
        }
        .button {
  display: inline-block;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  padding: 10px;
  border: none;
  font: normal 48px/normal "Comic Sans MS", Helvetica, sans-serif; 
  color: rgba(255,255,255,1);
  text-decoration: normal;
  text-align: center;
  -o-text-overflow: clip;
  text-overflow: clip;
  white-space: pre;
  text-shadow: 0 0 10px rgba(255,255,255,1) , 0 0 20px rgba(255,255,255,1) , 0 0 30px rgba(255,255,255,1) , 0 0 40px #ff00de , 0 0 70px #ff00de , 0 0 80px #ff00de , 0 0 100px #ff00de ;
  -webkit-transition: all 200ms cubic-bezier(0.42, 0, 0.58, 1);
  -moz-transition: all 200ms cubic-bezier(0.42, 0, 0.58, 1);
  -o-transition: all 200ms cubic-bezier(0.42, 0, 0.58, 1);
  transition: all 200ms cubic-bezier(0.42, 0, 0.58, 1);
}

.button:hover {
  text-shadow: 0 0 10px rgba(255,255,255,1) , 0 0 20px rgba(255,255,255,1) , 0 0 30px rgba(255,255,255,1) , 0 0 40px #00ffff , 0 0 70px #00ffff , 0 0 80px #00ffff , 0 0 100px #00ffff ;
}
    </style>
</head>
<body>
<img src='https://i.imgur.com/arWnOG3.jpg' style='position:fixed;top:0px;left:0px;width:100%;height:100%;z-index:-1;'>
<div class="container">
    <div class="button">#YouTube-Video-Downloader | AnhQuanCanHoang</div>
    <!--<p>#YouTubeVideoDownloader</p> -->
    <form action="" method="post" id="download-form">
        <div class="row">
            <div class="col-md-10">
                <div class="form-group <?= $isError ? 'has-error' : '' ?>">
                    <input id="video-url" title="Video url" type="text" name="url" placeholder="URL Video (Dạng: https://www.youtube.com/watch?v=)"
                           class="form-control" value="<?= htmlspecialchars($url) ?>"/>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <button class="btn btn-primary btn-block">Lấy link download</button>
                </div>
            </div>
        </div>
    </form>
    <div class="alert alert-danger" role="alert" id="error-block" style="display: <?= $isError ? 'block' : 'none' ?>">
        <?= $isError ? $response['error'] : '' ?>
    </div>

    <?php if ($isSuccessResponse): ?>
        <?php
        $baseInfo = $response['data']['baseInfo'];
        $downloadInfo = $response['data']['downloadInfo'];
        ?>
    <?php endif; ?>

    <h3 id="video-name">
        <?php if ($isSuccessResponse): ?>
            <?= htmlspecialchars($baseInfo['name']) ?>
        <?php endif; ?>
    </h3>

        <div class="col-md-6">
            <table id="download-list" class="table">
                <thead <?= !$isSuccessResponse ? 'style="display: none"' : '' ?>>
                <tr>
                    <th>Kiểu</th>
                    <th>Kích thước</th>
                    <th>Liên kết tải xuống</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($isSuccessResponse): ?>
                    <?php foreach ($downloadInfo AS $downloadInfoItem): ?>
                        <tr>
                            <td><?= $downloadInfoItem['fileType'] ?></td>
                            <td><?= $downloadInfoItem['fileSizeHuman'] ?></td>
                            <td>
                                <?php
                                    $downloadUrl = 'download.php?id=' . $videoId . '&itag=' . $downloadInfoItem['youtubeItag'];
                                ?>
                                <a
                                        href="<?= $downloadUrl ?>"
                                        target="_blank"
                                        class="btn btn-success"
                                >
                                    <span class="glyphicon glyphicon-circle-arrow-down"></span>
                                    Tải xuống
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    <div class="row">
        <div class="col-md-6" style="display: <?= $isSuccessResponse ? 'block' : 'none' ?>">
            <div id="video-preview">
                <?php if ($isSuccessResponse): ?>
                    <br/>
                    <br/>
            <iframe src="<?= $baseInfo['previewUrl'] ?>" width="600" height="400" >
                <?php endif; ?>
            </div>
            <pre id="video-description">
                <?php if ($isSuccessResponse): ?>
                    <?= htmlspecialchars($baseInfo['description']) ?>
                <?php endif; ?>
            </pre>
        </div>
    </div>
</div>
<footer>&nbsp;&nbsp;&nbsp;&copy; 2017 <a href="https://facebook.com/GodQ79">AnhQuanCanHoang</a></footer>
<div id="load-shadow"
     style="display: none; width: 100%; height: 100%; left: 0; top: 0; background: rgba(63, 0, 255, 0.31); z-index: 10; position: fixed;"></div>
</body>
<script src="//code.jquery.com/jquery-2.2.4.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script>
    jQuery(document).ready(function(){
   $('p').mousemove(function(e){
     var rXP = (e.pageX - this.offsetLeft-$(this).width()/2);
     var rYP = (e.pageY - this.offsetTop-$(this).height()/2);
     $('p').css('text-shadow', +rYP/10+'px '+rXP/80+'px rgba(227,6,19,.8), '+rYP/8+'px '+rXP/60+'px rgba(255,237,0,1), '+rXP/70+'px '+rYP/12+'px rgba(0,159,227,.7)');
   });
});
</script>
<!--<script src="js/youtube-downloader.js"></script>-->
</html>
