<?php
header("Content-Type:text/html;charset=utf-8");
include "./dbConnect.php";
$sql = "SELECT * FROM poll ORDER BY gap desc";
$res = $mysqli->query($sql);
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="db/swiper.min.css">
    <script src="db/swiper.min.js"></script>
    <script src="db/jquery-3.5.1.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@100;300;400;500;700&display=swap" rel="stylesheet">
    <title>POLL</title>
    <style>
        * {
            font-family: "Noto Sans KR";
        }

        body {
            padding: 80px 30px 30px 30px;
            background: #f5f6f7;
            margin:8px 0 8px 0;
        }

        .swiper-container {
            box-shadow: 0 0 18px black;
            width: 300px;
            height: 300px;
            border-radius: 46%;
        }
        <?php
        $idx=1;
        while($board = $res->fetch_array(MYSQLI_ASSOC)){?>
        .ss<?php echo $idx++; ?> {
            background: url('https://leed.at/db/image/<?php echo $board['idx']; ?>.jpg');
        }
        <?php }?>
        .swiper-slide {
            background-position: center center;
            background-repeat: no-repeat;
            background-size: cover;
        }
        #received{
            display:none;
            top: 93px;
            left: calc(50% + 124px);
            position: absolute;
            transform: translate(-50%);
            z-index: 1;
        }
        #receivedImg{
            width:80px;
            height:80px;   
        }
        .swiper-pagination{
            position:relative;
            top: -3px;
        }
        .swiper-pagination-bullet {
            margin: 0 13px;
            border-radius: 0;
            width: 9px;
            height: 4px;
        }

        .swiper-pagination-bullet-active {
            background: green;
        }
        .swiper-button-prev{
            top:228px;
            left:20px;
            --swiper-navigation-size: 30px;
        }
        .swiper-button-next{
            top:228px;
            right:20px;
            --swiper-navigation-size: 30px;
        }
        #content {
            text-align: center;
        }

        #cur_place {
            color: grey;
            font-size: 14px;
        }

        #name {
            font-size: 40px;
            font-weight: 200;
        }

        button {
            margin: 9px 0 0 0;
            border: none;
            height: 45px;
            border-radius: 2px;
            border-bottom: 1px solid black;
        }

        #poll_up {
            width: 50px;
        }

        #poll_down {
            width: 50px;
        }

        #notice {
            background: grey;
            box-shadow: 0 0 10px darkgrey;
            border-radius: 5px;
            position:absolute;
            width:80%;
            height: 40px;
            padding: 10px 0 0 12px;
            bottom:30px;
        }
        #notice_text{
            color:white;
        }
        .before {
            /*display:none;*/
            opacity: 0;
        }
        .move {
            opacity: 1;
            transform: translate(0, -10px);
            transition-duration: 0.5s;
        }

        @media (max-width: 1012px) {

        }
    </style>
</head>

<body>
    <div>
    <div class="swiper-container swiper1">
        <!--클래스 이름이 두개인거임ㅋㅋ-->
        <div class="swiper-wrapper">
            <div class="swiper-slide ss1"></div>
            <div class="swiper-slide ss2"></div>
            <div class="swiper-slide ss3"></div>
            <div class="swiper-slide ss4"></div>
            <div class="swiper-slide ss5"></div>
            <div class="swiper-slide ss6"></div>
            <div class="swiper-slide ss7"></div>
            <div class="swiper-slide ss8"></div>
            <div class="swiper-slide ss9"></div>
            <div class="swiper-slide ss10"></div>
        </div>
    </div>
    <!--페이지순서 - type-->
    <div class="swiper-pagination"></div>
    <!--이전 페이지-->
    <div class="swiper-button-prev"></div>
    <!--다음 페이지-->
    <div class="swiper-button-next"></div>
    </div>  
    <div id="received">
        <img src="./db/image/received.png" id="receivedImg">
    </div>
    <!-- If we need scrollbar<div class="swiper-scrollbar"></div>-->
    <div id="content">
        <span id="name"></span><span id="gap"></span><br>
        <span id="cur_place">
            현재
            <span id="place"></span>
            위를 달리고 있습니다</span><br>
        <button id="poll_up">업</button>
        <button id="poll_down">다운</button>
    </div>
    <div id="notice" class="before">
        <span id="notice_text">순위가 변동되었습니다. 새로고침 해주세요.</span>
    </div>
    <script>
        function move() {
            $('#notice').addClass('move');
        }
        $("#notice").slideDown();
        var curPage;
        var rank = [


        ];
        var lst = [];
        <?php
        $res = $mysqli->query($sql);
        while ($board = $res->fetch_array(MYSQLI_ASSOC)) { ?>
            rank.push(<?php echo $board['idx']; ?>);
            lst.push({
                "idx": "<?php echo $board['idx']; ?>",
                "name": "<?php echo $board['name']; ?>",
                "gap" : "<?php echo $board['gap']; ?>",
                "received" : "<?php echo $board['received']; ?>",
            });
        <?php } ?>
        $('#poll_up').click(function() {
            $('#gap').text('+' + ++lst[curPage]['gap']);
            update(1, lst[curPage]['idx']);
        });
        $('#poll_down').click(function() {
            $('#gap').text('+' + --lst[curPage]['gap']);
            update(0, lst[curPage]['idx']);
        });

        function update(updown, cur) {
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: 'https://leed.at/db/update.php',
                data: {
                    idx: cur,
                    order: updown
                },
                success: function(json) {
                    chkRank();
                }
            });
        }

        function chkRank() {
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: 'https://leed.at/db/chkrank.php',
                success: function(json) {
                    for (var i = 0; i < json.length; i++)
                        if (json[i] != rank[i]) {
                            console.log('다릅니다.');
                            move();
                        }
                },
                error: function() {
                    console.log('error');
                }
            });
        }
        // json에 extend해줌
        // db측 idx = list idx / this.activeIndex
        var swiper = new Swiper('.swiper1', {
            on: {
                slideChange: function() {
                    curPage = this.realIndex;
                    if(curPage == 0){
                        console.log(curPage);
                        $('.swiper-container').css('box-shadow', '0 0 20px #daa520');
                    }
                    else if(curPage == 1){
                        $('.swiper-container').css('box-shadow', '0 0 20px #9a9a9a');
                    }
                    else if(curPage==2){
                        $('.swiper-container').css('box-shadow', '0 0 20px #cd7f32');
                    }
                    else
                        $('.swiper-container').css('box-shadow', '0 0 10px black');
                    $('#name').text(lst[curPage]['name']);
                    if(parseInt(lst[curPage]['gap']) > 0)
                        $('#gap').text('+'+lst[curPage]['gap']);
                    else if(parseInt(lst[curPage]['gap']) < 0)
                        $('#gap').text(lst[curPage]['gap']);
                    else
                        $('#gap').text(lst[curPage]['gap']);
                    $('#place').text(curPage + 1);
                    if(lst[curPage]['received'] == '1'){
                        $('#received').css('display','block');
                    }else{
                        $('#received').css('display','none');
                    }
                }
            },
            loop: true,
            pagination: {
                el: '.swiper-pagination',
                type: 'bullets',
                /*renderBullet: function (index, className) {
          return '<span class="' + className + '">' + (menu[index]) + '</span>';*/
            },
            navigation: {
                prevEl: '.swiper-button-prev',
                nextEl: '.swiper-button-next',
            },
            scrollbar: {
                el: '.swiper-scrollbar',
                //draggable:true, 
                // IF true -> autoplay false
                hide: true,
            },
            keyboard: {
                enabled: true,
            },
            mousewheel: {
                invert: true,
            },
            centeredSlides: true,
            initialSlide: 0,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            effect : 'fade',
            fadeEffect: {
              
            },
            // 둘다 있어야 fade 적용됨 . . .
            //slidesPerView: 3,
            /*effect:'coverflow',
            coverflowEffect:{
                rotate: 0,
                //stretch: 50,
                depth: 250,
                modifier: 1,
                slideShadows : false,
            }*/
            //slidesPerView: 3, // 보여지는 슬라이드 수
            //spaceBetween: 0,
        });
    </script>
</body>

</html>