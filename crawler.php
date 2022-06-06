<?php
include_once($_SERVER["DOCUMENT_ROOT"].'/function.php');
include_once($_SERVER["DOCUMENT_ROOT"].'/db_config.php');
include_once($_SERVER["DOCUMENT_ROOT"]."/Snoopy.class.php");
?>

<?php
function sendTelegram ($message, $chatId) {
    $url = "https://api.telegram.org/botToken/sendmessage?chat_id=".$chatId."&parse_mode=html&text="; 
    $parseMessage = urlencode($message);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $url.$parseMessage
    ));
    $resp = curl_exec($curl);
}

function getSamrak ($date, $chatId) {
    $url = 'https://nakdongcamping.com/reservation/real_time?user_id=gg8820&site_id=&site_type=&dis_rate=0&user_dis_rate=&resdate='.$date.'&schGugun=1&price=0&bagprice=2000&allprice=0';
    $snoopy = new Snoopy;
    $snoopy->fetch($url);
    $file = $snoopy->results;

    $match1='/<a href="" class="cbtn  area_(a|b)  cbtn_[0-9][0-9]  cbtn_on  ">(.*)/';
    preg_match_all($match1, $file, $a1);
    $count = count($a1[0]);

    for($i=0; $i<$count; $i++){
        $site = strip_tags($a1[1][$i].$a1[0][$i]);
        $message = "[삼락생태공원 오토캠핑장]\n날짜 : ".$date."\n사이트 : ".$site."\n바로가기 : https://nakdongcamping.com/reservation/real_time?user_id=gg8820&site_id=&site_type=&dis_rate=0&user_dis_rate=&resdate=".$date."&schGugun=1&price=0&bagprice=2000&allprice=0";
        sendTelegram($message, $chatId);
    }
}

function getDaejeo ($date, $continueDay, $chatId) {
    $url = 'https://www.daejeocamping.com/reservation/real_time?user_id=&site_id=&site_type=&dis_rate=0&user_dis_rate=&resdate='.$date.'&schGugun='.$continueDay.'&price=0&bagprice=2000&allprice=0';
    $snoopy = new Snoopy;
    $snoopy->agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36';
    $snoopy->fetch($url);
    $file = $snoopy->results;

    $match1 = '/<a href="" class="cbtn  area_[b|c|d]  cbtn_(.*)  cbtn_on  ">/';
    preg_match_all($match1, $file, $a1);
    $count = count($a1[1]);
    print_r($count);

    for($i=0;$i<$count;$i++){
        $site = $a1[1][$i];
        if($site != 112) {
            $message = "[대저캠핑장]\n날짜 : ".$date."\n일수 : ".$continueDay."\n사이트 : ".$site."\n바로가기 : https://www.daejeocamping.com/reservation/real_time?user_id=&site_id=&site_type=&dis_rate=0&user_dis_rate=&resdate=".$date."&schGugun=".$continueDay."&price=0&bagprice=2000&allprice=0";
            sendTelegram($message, $chatId);
        }
    }
}

function getHwamyeong ($date, $continueDay, $chatId) {
    $url = 'https://www.hwamyungcamping.com/reservation/real_time?user_id=&site_id=&site_type=&dis_rate=0&user_dis_rate=&resdate='.$date.'&schGugun='.$continueDay.'&price=0&bagprice=2000&allprice=0';
    $snoopy = new Snoopy;
    $snoopy->fetch($url);
    $file = $snoopy->results;

    $match1='/<a href="" class="cbtn cbtn_[0-9][0-9]  cbtn_on  ">(.*)/';
    preg_match_all($match1, $file, $a1);
    $count = count($a1[0]);

    for($i=0; $i<$count; $i++){
        $site = $a1[1][$i];
        $message = "[화명 오토캠핑장]\n날짜 : ".$date."\n일수 : ".$continueDay."\n사이트 : ".$site."\n바로가기 : https://www.hwamyungcamping.com/reservation/real_time?user_id=&site_id=&site_type=&dis_rate=0&user_dis_rate=&resdate=".$date."&schGugun=".$continueDay."&price=0&bagprice=2000&allprice=0";
        sendTelegram($message, $chatId);
    }
}


function getGaon ($date, $chatId) {
    $url = 'http://www.gaonvillage.com/reservaion';
    $snoopy = new Snoopy;
    $snoopy->fetch($url);
    $file = $snoopy->results;

    $match1 = "/idx=[0-9]+&day=".$date."\" class=\"tabled full-width\" onclick=\"\"/";
    preg_match_all($match1, $file, $a1);
    $count = count($a1[0]);

    for($i=0; $i<$count; $i++){
        $site = str_replace("idx=", "", explode("&day", $a1[0][$i])[0]);
        $message = "[가온빌리지]\n날짜 : ".$date."\n사이트 : ".$site."\n바로가기 : http://www.gaonvillage.com/reservaion/?idx=".$site."&day=".$date;
        sendTelegram($message, $chatId);
    }
}


function getHealing ($date, $continueDay, $chatId) {
    $date = str_replace("-", "", $date);
    $url = "https://ticket.interpark.com/PCampingBook/BookDatetime.asp?BizCode=42088&GoodsCode=21004842&PlaceCode=21000348&PlayDate=".$date."&CheckOutDate=&YM=";

    $snoopy = new Snoopy;
    $snoopy->agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36";
    $snoopy->fetch($url);
    $file = $snoopy->results;

    //$match1 = '/\''.$date.'\', \'(.*)\'\)"  class="selOn"/';
    $match1 = '/\''.$date.'\', \'(.*)\'\)" id="book_link" class="selOn"/';
    preg_match_all($match1, $file, $a1);

    if (count($a1[1]) <= 0) return; //값이 없으면 return;
    $seq = $a1[1][0];
    if ($continueDay > 1) $seq = $seq.','.($seq+1); //장박일경우 값추가

    $url = 'https://ticket.interpark.com/PCampingBook/Lib/BookInfoXml.asp?Flag=SeatGradeCnt&GoodsCode=21004842&PlaceCode=21000348&BizCode=42955&PlaySeqList='.$seq;
    $snoopy->fetch($url);
    $file = $snoopy->results;
    $xml = simplexml_load_string($file);
    $count1 = $xml->Table[0]->RemainCnt; //데크사이트
    $count2 = $xml->Table[1]->RemainCnt; //오토사이트

    if ($count1 > 0) {
        $message = "[부산항힐링야영장]\n날짜 : ".$date."\n데크사이트 : ".$count1."개\n일수 : ".$continueDay."\n바로가기 : https://www.busanpa.com/redevelopment/Board.do?mCode=MN0082";
        sendTelegram($message, $chatId);
    }

    if($count2 > 0) {
        $message = "[부산항힐링야영장]\n날짜 : ".$date."\n오토사이트 : ".$count2."개\n일수 : ".$continueDay."\n바로가기 : https://www.busanpa.com/redevelopment/Board.do?mCode=MN0082";
        sendTelegram($message, $chatId);
    }
}

function getCheonmundae ($date, $chatId) {
    $date_to_unix = strtotime($date);
    $url = "http://www.cheonmundaecamping.kr/inc/quick_reserve1_room.php?sdate=".$date_to_unix;
    echo $url;
    $snoopy = new Snoopy;
    $snoopy->fetch($url);
    $file = $snoopy->results;
    $file=iconv("euc-kr","UTF-8",$file);

    $match1 = '/;">(.*)<\/li>/';
    preg_match_all($match1, $file, $m1);
    $count = count($m1[1]);
    for ($i=0;$i<$count;$i++) {
        $site = $m1[1][$i];
        if (strpos($site, '펜션') === false) {
            $message = "[천문대캠핑장]\n날짜 : ".$date."\n사이트 : ".$site."\n바로가기 : http://www.cheonmundaecamping.kr/inc/quick_reserve.html";
            sendTelegram($message, $chatId);
        }
    }
}

function getHwangsan ($date, $chatId) {
    $url = 'https://hscamping.yssisul.or.kr:453/inc/getSrmGrp.html?b_id=hscamping&evtMode=&slh_idx=48&ss_date='.$date;
    $snoopy = new Snoopy;
    $snoopy->agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36';
    $snoopy->fetch($url);

    $file = $snoopy->results;
    $xml = simplexml_load_string($file);
    $result = $xml->srmGrpInfo;

    $match1='/<b>오토캠핑장 \((.*)\)<\/b>/';
    preg_match_all($match1, $result, $a1);
    $count1 = $a1[1][0];

    $match2='/<b>일반캠핑장 \((.*)\)<\/b>/';
    preg_match_all($match2, $result, $a2);
    print_r($a2);
    $count2 = $a2[1][0];

    if ($count1 > 0) {
        $message = "[황산캠핑장]\n날짜 : ".$date."\n오토캠핑장 : ".$count1."개\n바로가기 : https://hscamping.yssisul.or.kr:453/rsvc/rsv_srm.html?b_id=hscamping";
        sendTelegram($message, $chatId);
    }

    if($count2 > 0) {
        $message = "[황산캠핑장]\n날짜 : ".$date."\n일반캠핑장 : ".$count2."개\n바로가기 : https://hscamping.yssisul.or.kr:453/rsvc/rsv_srm.html?b_id=hscamping";
        sendTelegram($message, $chatId);
    }
}

function getHwangsanAuto($date, $continueDay, $chatId) {
    $url = "https://hscamping.yssisul.or.kr:453/inc/getSrmArea.html?b_id=hscamping&sgp_idx=1&ss_date=".$date."&evtMode=&slh_idx=&vwSaleMaxDay=".$continueDay;
    $snoopy = new Snoopy;
    $snoopy->fetch($url);
    $file = $snoopy->results;
    $match1 = '/title="오토캠핑\-[0-9]{2} \(선택가능\)"/';
    preg_match_all($match1, $file, $a1);
    $count = count($a1[0]);
    for ($i=0; $i<$count; $i++) {
        $place = str_replace(' (선택가능)"', "", str_replace('title="', "", $a1[0][$i]));
        $message = "[황산캠핑장(오토)]\n날짜 : ".$date."\n사이트".$place."\n일수 : ".$continueDay."\n바로가기 : https://hscamping.yssisul.or.kr:453/rsvc/rsv_srm.html?b_id=hscamping";
        sendTelegram($message, $chatId);
    }
}

$today = date('Y-m-d');
$sql = "SELECT * FROM cp_monitor WHERE m_reserve_date >= '{$today}'";
$rst = mysqli_query($conn_cp, $sql);
while ($row = mysqli_fetch_array($rst)) {
    $reserveDate = $row['m_reserve_date'];
    $chatId = $row['m_chat_id'];
    $placeCode = $row['m_place_code'];
    $continueDay = $row['m_continue_day']; //연속일 기본값은 1

    switch ($placeCode) {
        case 'SR' :
            getSamrak($reserveDate, $chatId);
            break;
        case 'CMD' :
            getCheonmundae($reserveDate, $chatId);
            break;
        case 'HM' :
            getHwamyeong($reserveDate, $continueDay, $chatId);
            break;
        case 'DJ' :
            getDaejeo($reserveDate, $continueDay, $chatId);
            break;
        case 'GV' :
            getGaon($reserveDate, $chatId);
            break;
        case 'BH' :
            getHealing($reserveDate, $continueDay, $chatId);
            break;
        case 'HS' :
            getHwangsan($reserveDate, $chatId);
            break;
        case 'HSA' :
            getHwangsanAuto($reserveDate, $continueDay, $chatId);
            break;
        default :
            break;
    }
}
?>
