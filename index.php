<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$origin = $_REQUEST['origin'];
$destination = $_REQUEST['destination'];
$passengers = $_REQUEST['passengers'];

$token_raw = file_get_contents("https://api.primeflightz.com/token/");

$token = json_decode($token_raw, true)['token'];
$cookie = json_decode($token_raw, true)['cookie'];

if(empty($origin)){
    $return['type'] = 'error';
    $return['message'] = 'origin is required';
    die(json_encode($return));
} elseif (empty($destination)){
    $return['type'] = 'error';
    $return['message'] = 'destination is required';
    die(json_encode($return));
} elseif (empty($token)){
    $return['type'] = 'error';
    $return['message'] = 'token is required';
    die(json_encode($return));
} elseif (empty($passengers)){
    $return['type'] = 'error';
    $return['message'] = 'passengers is required';
    die(json_encode($return));
}




$calendar_from = (new DateTime('first day of this month'))
    ->format('Y-m-d');

$calendar_to = (new DateTime('last day of next month'))
    ->format('Y-m-d');


$request_string = '{"searchType":"SMR","searchTypePrioritized":"SSA","origin":"' . strtoupper($origin) . '","destination":"' . strtoupper($destination) . '","cabinCode":"1","departingDate":"2025-09-13","toDepartingDate":"2025-10-31","ftNum":"","internationalization":{"language":"en","country":"wr","currency":"usd"},"idCoti":"900020949019598088","ip":"","isReturning":false,"paxNum":' . $passengers . ',"promotionCodes":["CORE008"],"discounts":[],"sch":{"schHcfltrc":"RTHl16zCyvVmyqmJG1UR079TJlEJzKWXKGhXUFMPYyI=","schLmidLtrc":"8WEgrX46Hx0Z4G6FTiNhcLy8q23JBZIiV4UJ+4QCjX0=","schPIR":"axKDLp+XfxAGufnBGJWFjJ620Zm745u61T5Sl7SuAJk=","schPcFlt":"jyOd0GAZeE/ZAHMxtRQSWf5OqPd2SrvDe61kr44gq3E=","schTr":"hp1D+cLL+mK59uyQlgOY9g=="}}';

echo $token;
echo '/n/r <br>';
echo '/n/r <br>';
echo '/n/r <br>';
echo $cookie;
return;

$calendar_raw = get_calendar($token, $cookie, $request_string);


//echo $calendar_raw;
$calendar_json = json_decode($calendar_raw, true);


$available_dates = [];

if (!empty($calendar_json['flights'])) {
    foreach ($calendar_json['flights'] as $flight) {
        if (!empty($flight['departingDatesData'])) {
            foreach ($flight['departingDatesData'] as $departing) {
                $year = $departing['year'];

                foreach ($departing['months'] as $month) {
                    $monthNum = str_pad($month['number'], 2, '0', STR_PAD_LEFT);

                    foreach ($month['valueFares'] as $day) {
                        $dayNum = str_pad($day, 2, '0', STR_PAD_LEFT);
                        $available_dates[] = $year . "-" . $monthNum . "-" . $dayNum;
                    }
                }
            }
        }
    }
}


$return['type'] = 'success';
$return['origin'] = $origin;
$return['destination'] = $destination;
$return['available_dates'] = $available_dates;

echo json_encode($return);

exit;


function get_calendar($token, $cookie, $request_string){
try {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.lifemiles.com/svc/air-redemption-cal-calendar-private',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $request_string,
        CURLOPT_HTTPHEADER => array(
            'accept: application/json',
            'authorization: ' . $token,
            'cache-control: no-cache',
            'content-type: application/json',
            'origin: https://www.lifemiles.com',
            'pragma: no-cache',
            'priority: u=1, i',
            'realm: lifemiles',
            'referer: https://www.lifemiles.com/',
            'sec-ch-ua: "Chromium";v="140", "Not=A?Brand";v="24", "Google Chrome";v="140"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Windows"',
            'sec-fetch-dest: empty',
            'sec-fetch-mode: cors',
            'sec-fetch-site: same-site',
            'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36',
            'Cookie: ' . $cookie
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;


    //code...
} catch (\Throwable $th) {
   return json_encode($th);
}

}

function test_calendar(){


    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.lifemiles.com/svc/air-redemption-cal-calendar-private',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{"searchType":"SMR","searchTypePrioritized":"SSA","origin":"BKK","destination":"LCY","cabinCode":"1","departingDate":"2025-09-13","toDepartingDate":"2025-10-31","ftNum":"","internationalization":{"language":"en","country":"wr","currency":"usd"},"idCoti":"900020949019598088","ip":"","isReturning":false,"paxNum":1,"promotionCodes":["CORE008"],"discounts":[],"sch":{"schHcfltrc":"RTHl16zCyvVmyqmJG1UR079TJlEJzKWXKGhXUFMPYyI=","schLmidLtrc":"8WEgrX46Hx0Z4G6FTiNhcLy8q23JBZIiV4UJ+4QCjX0=","schPIR":"axKDLp+XfxAGufnBGJWFjJ620Zm745u61T5Sl7SuAJk=","schPcFlt":"jyOd0GAZeE/ZAHMxtRQSWf5OqPd2SrvDe61kr44gq3E=","schTr":"hp1D+cLL+mK59uyQlgOY9g=="}}',
        CURLOPT_HTTPHEADER => array(
            'accept: application/json',
            'accept-language: bg-BG,bg;q=0.9,en-BG;q=0.8,en;q=0.7',
            'authorization: Bearer eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJGNVg5Y0Zoa3ZVOU5nRVBnNmhrU0FBMVVkWlpwY2lVaEc4dm1NY1YyTUtvIn0.eyJleHAiOjE3NTc2NjkyODgsImlhdCI6MTc1NzY2ODk4OCwianRpIjoiNjA2M2NlMjctMDlkOC00NmI0LThjNjAtZjA0NzQwZjM3YzEwIiwiaXNzIjoiaHR0cHM6Ly9zc28ubGlmZW1pbGVzLmNvbS9hdXRoL3JlYWxtcy9saWZlbWlsZXMiLCJhdWQiOiJhY2NvdW50IiwidHlwIjoiQmVhcmVyIiwiYXpwIjoibGlmZW1pbGVzIiwic2lkIjoiZDk0YjhmYjctZGRhOS00NjRlLTkzODEtYTJjNmVmMjZiZWMwIiwiYWxsb3dlZC1vcmlnaW5zIjpbImh0dHBzOi8vd3d3LmxpZmVtaWxlcy5jb20iLCJodHRwczovL3d3dy5saWZlbWlsZXMuY29tLyJdLCJyZWFsbV9hY2Nlc3MiOnsicm9sZXMiOlsib2ZmbGluZV9hY2Nlc3MiLCJkZWZhdWx0LXJvbGVzLWxpZmVtaWxlcyIsInVtYV9hdXRob3JpemF0aW9uIl19LCJyZXNvdXJjZV9hY2Nlc3MiOnsiYWNjb3VudCI6eyJyb2xlcyI6WyJtYW5hZ2UtYWNjb3VudCIsIm1hbmFnZS1hY2NvdW50LWxpbmtzIiwidmlldy1wcm9maWxlIl19fSwic2NvcGUiOiJvcGVuaWQgZW1haWwgcHJvZmlsZSBsaWZlbWlsZXMtbXAiLCJsbS1pZCI6IjAwMDQwMDI0OTAyIiwiZW1haWxfdmVyaWZpZWQiOmZhbHNlLCJwcm92aWRlciI6ImxpZmVtaWxlcyIsIm5hbWUiOiJDaHJpc3RlbmUgUml2ZXJhIiwicHJlZmVycmVkX3VzZXJuYW1lIjoibG91aWZyYW5jaXNjbzkxQG91dGxvb2suY29tIiwiZ2l2ZW5fbmFtZSI6IkNocmlzdGVuZSIsImZhbWlseV9uYW1lIjoiUml2ZXJhIiwidGlkIjoiNGI1NjEwZmE2ZDRmNzk4YWVlZDAwYjg4YjQwMTc1NmMxYTU0Mzg3MzEyNjVkMTc5ZWI2ZGE2MWQyMjBjMzU0NyIsImVtYWlsIjoibG91aWZyYW5jaXNjbzkxQG91dGxvb2suY29tIiwiY2lkIjoibGlmZW1pbGVzIn0.OYSCFQsDfpDdOoPWVm6H6qB16L0LGLIUmreofGq-t3aowIFJgSgygeCWob7QpOZW8ubQ8x0dg8nBdMKI2FuK5QLJQaKth9ARRdDshupdSNE6BwG0LFOsvqoqF5BNae_U7tCnklQKpY7guEZN8udW8hDLjeYuhRvhjm5h_g3Rhc8tuXPKkl4bdDM9Zb3MZ7NhoIAJFwb5XS7R9nPfaQSsRM8IfAEadOnCaUoTf1hBWI55sYozQqdxl5h9br6pF4BGp3vP6QuHR-auoUqC47k0JVJ3JoHocmy3o0936O4tZJWaN9zidme8tBg98wwBVKcCHHVW10yFNc67LYkJ03TnNg',
            'cache-control: no-cache',
            'content-type: application/json',
            'origin: https://www.lifemiles.com',
            'pragma: no-cache',
            'priority: u=1, i',
            'realm: lifemiles',
            'referer: https://www.lifemiles.com/',
            'sec-ch-ua: "Chromium";v="140", "Not=A?Brand";v="24", "Google Chrome";v="140"',
            'sec-ch-ua-mobile: ?0',
            'sec-ch-ua-platform: "Windows"',
            'sec-fetch-dest: empty',
            'sec-fetch-mode: cors',
            'sec-fetch-site: same-site',
            'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36',
            'Cookie: _gcl_au=1.1.387143440.1754402661; _tt_enable_cookie=1; _ttp=01K1X8R942N326Y663HJZMNHXG_.tt.1; _fbp=fb.1.1754402661942.258499360616986039; cty-stg=wr; FPID=FPID2.2.mSWh9HsNLMmC%2Fj6UpvXRCI4wwZlzO1VOLot1F0heceo%3D.1754402661; _ga_BS8PBT1VYN=GS2.2.s1756820908$o1$g0$t1756820977$j60$l0$h0; _ga_LWDNL0ZMW0=GS2.1.s1756820908$o1$g0$t1756820978$j59$l0$h0; _ga=GA1.1.1871087096.1754402661; __ssid=866a8d8f7d7c64f355001c8d55a29f7; rxVisitor=1756894076863VGB2550SJFJMDUODU9CMIQJJT9AB41AV; ak_bmsc=A38215AB6DA454AB1F5B232910E1194E~000000000000000000000000000000~YAAQDi0UAsiiAu2YAQAAzMo7PR1wZKLQXjzY5UIcKHkovUYT21r1Y+yFsA037Gl6F6JaXPEY+b6J6noOuLi1YCWoSHSH67F6FTNbx3eO3RbCTiGZlhBN6Fg/iLDmkls7P9vRPMrul+Yt19qX4+o8IBHyTb1Rv/296ZTYZkrbc5AIB3Cxi0f+P4M91Ro2srmCbekNa5JX49l8PYyRPTWS9iDYBd9UZdI3uYz2hEuP2G70Vqubqnzx6AA0K4DLKAZONDh6mqJB4AH6uogLZ81fdtJ5RQQz/VJyWkRqidrX/2UndhfNeuaEwbQDCqMIwRvpeI4M+UDfcDxC98xOW9f1aUjmbV+7X8wAewmjITyoPUI0PeguMwmJI7XmYvM4VfcwnObOrjOA9LSyMHxN9ovtGQPzK7p7rC5+9mjT+le8du4ZC3BMHhhD54IU38iJTSe+HTmIVbn4yvUtwW7uvvFvjjs=; cry-stg=usd; chn-stg=wst; AMCVS_E9273E7759A886E20A495C18%40AdobeOrg=1; AMCV_E9273E7759A886E20A495C18%40AdobeOrg=179643557%7CMCIDTS%7C20344%7CMCMID%7C28391444810581978970100832267006863574%7CMCAAMLH-1758273756%7C6%7CMCAAMB-1758273756%7CRKhpRz8krg2tLO6pguXWp5olkAcUniQYPHaMWWgdJ3xzPWQmdj0y%7CMCOPTOUT-1757676156s%7CNONE%7CvVersion%7C5.5.0; kndctr_E9273E7759A886E20A495C18_AdobeOrg_cluster=irl1; kndctr_E9273E7759A886E20A495C18_AdobeOrg_identity=CiYyODM5MTQ0NDgxMDU4MTk3ODk3MDEwMDgzMjI2NzAwNjg2MzU3NFIRCLSCsfiQMxgBKgRJUkwxMAPwAea27-mTMw==; lng-stg=en; FPLC=YC5dsxLmYizhTyqXWVPgwvGnSxpv2TzQuOaiagznQqPyq91zaDLpfJwx1AdhMceAhpOOr4Chu8F8v%2BCO%2Bk0BX3NFtqx9HmVboc9cNZSvc6buSEnSKRkwFz6Vj5fTIg%3D%3D; _ga_YYYYYYYYYY=GS2.1.s1757668963$o16$g0$t1757668987$j36$l0$h1488406291; _ga_E7W2MLTW89=GS2.1.s1757668957$o16$g1$t1757668990$j27$l0$h0; _ga_P7JFFQ1HW2=GS2.1.s1757668957$o16$g1$t1757668990$j27$l0$h0; ttcsid=1757668957988::plo3ytnKKkUL2gRT4FSo.14.1757668991157; dtCookie=v_4_srv_11_sn_B69492235C514338A56FE0B19901ADF1_perc_100000_ol_0_mul_1_app-3A93fb5a7425baf99b_0; ttcsid_CR8AFN3C77UA2RB20G80=1757668957987::o0e2uJAio73PajtOuz4l.14.1757669000845; ttcsid_CVQLH6JC77U9V6QA3ING=1757668957990::56Ia8XbS0QQrN-K2eDC1.14.1757669000845; ttcsid_CRCTMD3C77UB15K02B8G=1757668957989::bNdfjJKRGiaQe6wjClrv.14.1757669000845; _abck=535350070CA80422540B139D4101D713~0~YAAQDi0UAkKnAu2YAQAAh6A8PQ5BTxqIs7AixojxMeAc5w1mylzWotOEXDBoLgC9NDfGJILhU7VQYmHHREEt5f1DSG88gUkRaLzH/89ywInWi56g6q+Vw+kwklfq7kCYxJxXYJT60/9osx0Whg9v2FToGss8bBF5JIgCwXihgpmdqZAWMVcXIQcvfmTaIEcMfXq2G4iVziJTD4blSJqLBAFL49vjwhjcID66ycPxeswKmRScysSFpcFFifqrLMBpqc/8EvDy083FflSVA32NFU/lgxU7SrWdsQ2BbBLoHXXxP5vV/S55fQnH08SyPBCubsnueATYBdcKQarj1pLWExkQBf6a5kUxV3NkWSGxFDaxd+6pJPUSNScUqCIKz4Au55rdaBi+jYp/M7XwtGnBB2JS24bF6cqNXR5BZrop1TjaWYZc13mY1jPIPs6MbiMLqezQmgdgj23OwTlRFcs/goyqp1fuc7VtcXs42bPLaYBQVpm6J4dFdCPL//u/QUc6Kdci0qWMtYEozRAftVa/th8K18G19ha42pvlXDujLX88qsmahPa3Gm6t+MEsuDNxMH3ws3FuyLue9dXzUbwK46xDih1RzFoz51U4V6R38kybS5CY+kLxCC0VJpHDaY/zUwEGpM3sVWPWlArEdfNGxLcR4KScB6ZBwJ4Pl2mEzSoCd4sgW5oDo0Ib7KiMt4wsdyGaZa6AU8kX6z6RlSvH8qS/NqoKJVYdNZgPVai4xvzT8/P+1AyinguUDS5HKupbqDgrPB53aCMh0aqeUwqFDDg1ummZ3QVwzWRYWEkRD38W2B5HIG/qy4b9SpcUi1rJLpP7yWq5FQxrUkmz1RjffpYZY+iOu50udAxIDA==~-1~-1~-1~AAQAAAAE%2f%2f%2f%2f%2f0+k0E2CiSlVLUA0yOqRNKofY%2fxg6Boqpuk7UGzejH0hAo8qTAqBLpdc6dm66z7ua%2fD7jw6tJA0onu2ffATNFEk+T9y+NEcFSTu1~-1; bm_sv=AE502FE3B4B4862176AE7486C513F42F~YAAQDi0UAkOnAu2YAQAAh6A8PR16v4q2HT1gqrKgT1cHDmspI/mBm56ASY4MFaEkWckwfrBH1YfCb6VsHAgEtEKFuJZbNO0FF+/o7T/qFoubm8M0KdhTim2tgfWWq0aXFviP0FrvRKUlZ2hOLulcl6B3TaZT7B4LRs6qPf/mAZZU+V3PPfHT47cXUldVT3A7JYxNdEJzcMj+GqGV+CwxOTfhJQmeYpsH13kHrME2c2oNJomQ8BaH00AX342eLA1jf0iQ/w==~1; bm_sz=2AB4353C72F4BF4FDA7C1FEAC2A6651A~YAAQDi0UAkSnAu2YAQAAh6A8PR1bJsAdyEv/Vqpm7BgAEH59pH7Yrw+bDWQjlyvpOlRV68aCPUGTy4dTSSi5AeiMQGaXtbMmC3Atz/Bg3QMb2GjRkk5YshZ/HIruaezmDfwTrQZcD/T0RjHAtQna8oS6fXO6MWXqi/tUFOzJ8hotd2G7mZrxtPpDs+tb5YVga3KH+6K0MxZpLbYuPcBpspF/ZUBsXy9IBktMmHPv6Fl6buQL8CnBz8HvcczUhECZN+/W79sEJ8gfFKXxar3KFA67D7jzYMoq/6i/5kdXlF2IkCTwT1GeKHHqIOFSvNZUYeLTRiQrxRDBkOgJKWLWppvyBUyQGbkdqoahsRy5DvXWhSCYWSSEpEsDAQWVKJO9eon3upU5rjTy3zrlDWcspB2iSCAqPPpRmKQNlCRk2PzZOTVzaauHsrDw7K6CRcVkamwYxQS1rbKoaU8=~3228976~4337986; _abck=535350070CA80422540B139D4101D713~-1~YAAQBy0UAgzAhzSZAQAAAvw8PQ4w9Xoq55D3r2odsLH4tsa3+yQ7RnfSy1hFXPsd9kuAd/VuCFhKktPDdmn7tmt4P11XKodWQK6HI0/bxfrckP/LKQn44DsCEIztOLoERw/S6C8Jd8AImpn8PhbgQzolISLATB6q4FDIOqVeLHgGd/ssH8VS57iKsTEicAKp2pw8ZafFqWJJSxrfGNCeK0jR3v/Y2plRfZR6Z/al/7GRpY6KURBdcRdSQ0Ho7Q90ZYt1AjH4ex91AGyTOupXrL1IKAzmUU4CWf+cZhmtPH7MB+q5hsG6pKove2J4FuhHBsZRVKcG3tXOzieSgSeTzOp+MocQ2dVLMz0nF+zc6nDYFKnHDNR5PliyZERAZoNWbyy5kDCEYxH5ZeKV4ZuyTAOd77hkGvGuUf+fQKEgrT6XjOsDkVi+vb8h8dp7V3G05U49Mwfev2o3zulVuiY4IBKVGOxBp9JJaA/SnnNYGpfuy5LKBzxhC8IsgyW8LJbFGzHiym/CKrpL40OEbSt4f/Mq+wZbPWK8VKNa16c4GCft3lpRXu+OG7dySqRx8WtJczpe0qfC1RWiYH6/9LzS+LMw4sfdfhHgj57zjVfkWU87nW2sUjqZw1mmAkIdCqXhrCENwcBZcp5kC6O5oZ1uPALvPtcnIQvpVoFIoadFhrr4q1KSKIkw/MxhCW+OhWAJnb41CNCBKqp2fKMsThLSIO0y+/WZuRD2UKD8Rrp4NbKydiZJv3MVAhOd9BYcXsztsfuM2I0maezU2Su1gLbGpEk/rWbeEM7IK3TDKWSSGK8i6UgUGqxYgJg7Rh270Qg4IJ0qcK80bXwdokzPizoc5qm3UTkr2Aw2c7GObf/7m+RYElPwUcjfqO/RWEVbAEAWwDBigvzufYIQBH0qki7zRPJcmg+lfqrD6lrNlpHo2MsQRuiOnSmo5lu0~0~-1~-1~AAQAAAAE%2f%2f%2f%2f%2f0+k0E2CiSlVLUA0yOqRNKofY%2fxg6Boqpuk7UGzejH0hAo8qTAqBLpdc6dm66z7ua%2fD7jw6tJA0onu2ffATNFEk+T9y+NEcFSTu1~-1; bm_sv=AE502FE3B4B4862176AE7486C513F42F~YAAQBy0UAg3AhzSZAQAAAvw8PR1QHH3tPQ2TCLUK82EzzARHs7mRA+kBS3nzDOe7xVs6KzDGOyEjzXUFWC7aQONv0lOwWrsBgPa9E+GtzKg/XukjzJVf6DnUIRsD4V/fzwtcK7m0S1UzBqGoncg16KMg/eha7zUZfcdhPimCdB4EU2vGcUhY7Ihk5yvg0qQ2juol5jMLX6wMhLzoRDbcZTkvR6Lq4kBkIgr4EPMvZxbY4SOr+lkI73dOoGyR25e0Fm0/uw==~1; bm_sz=2AB4353C72F4BF4FDA7C1FEAC2A6651A~YAAQBy0UAg7AhzSZAQAAAvw8PR2G4VB3u6YvKXbwgd3HCxy9iQMfNX6RTybxRkuCnP66AwYOjCT1PcjIRTY+Jf2gJC/IUL5AdlhM2I4rv4rWOT7GRYgSD12KsynUvv36Zz4U78u4752N+LXOYUzHnyBrgvaKPlV+t3KJkScdOzWnknX7xbK6BAtNjIMSk+nFLZSesgOsPQbZ+SwJPRagrS4LHygWz9707shhyHsth2GfJFLlRnZBxHTpNN5wwsNngTw5YuNs+36fQscoBexYsD8LJhyDpPUwkQfa5ha+e2fq1EHaRn+6HEZcA+u5MLHbKj5PMA8umE45ZO4ocA+VHpOc4tJ7Lp6yaM0oKS9oItnc5P0vYLGFwdD/Qd3DhGyKk03AZd3KQRxHb8uy8V8dgeWFK+6miTYttw4Nrs0Po1+7L8eA6STFRn0/WO8J/AqmbDMVZrSpLgnzGt26AySV/+Ye~3228976~4337986'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;


}