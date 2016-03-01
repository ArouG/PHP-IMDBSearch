<?php              
 //////////////////////////////////////////////////
 //         IMDBSearch : find on IMDB.COM some titles like an input title
 //         INPUT :
 //              a title ($_GET['t']) 
 //              a country ($_GET['c']) : in order to filter all the titles of the 'movies'
 //              a MoviesTypes ($_GET['g'] & see langages.php : $Types) : to filter only on some 'MoviesTypes'
 //         OUTPUT : array of {
 //                     imdb_id   : something like tt0456201 <- Id IMDB of the movie
 //                     title     : principal title (depend on country),
 //                     year      : IMDB year of the movie,
 //                     genres    : (string) IMDB genre(s),
 //                     directors : (string) IMDB director(s),
 //                     poster    : (string / URL) the biggest IMAGE if exists,
 //                     AKAs      : (string) IMDB also_known_as (depend too on INPUT country),
 //                     video     : (string / URL) IMDB trailer if exists,
 //                     type      : IMDB MoviesTypes,
 //                     pere      : IMDB_id for the serie if type=episode,
 //                 } 
 //                    OR '0' if no movie match on IMDb.com
 //          ATTENTION : strings in output have '~~' in place of "'"
 //          nota : with imdb.class.php / langages.php (internationalisation)
 //          adapted from https://github.com/abhinayrathore/PHP-IMDb-Scraper
 ////////////////////////////////////////////////////                                
set_time_limit(0);
error_reporting(E_ERROR | E_WARNING | E_PARSE);  
ini_set("memory_limit" , -1); 

require('langages.php');
include("imdb.class.php");

////////////////////////////////////////////////////
//   for debugging only
////////////////////////////////////////////////////
function file_ecrit($filename,$data)
  {
    if($fp = fopen($filename,'a'))   // mode ajout !!
    {
      $ok = fwrite($fp,$data);
      fclose($fp);
      return $ok;
    }
    else return false;
  }        

function get_ip()
  { 
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
      { 
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      } 
    elseif (isset($_SERVER['HTTP_CLIENT_IP']))
      { 
        $ip = $_SERVER['HTTP_CLIENT_IP'];
      } 
    else
      { 
        $ip = $_SERVER['REMOTE_ADDR'];
      } 
    return $ip;
  }
  
    $myip=get_ip();
    file_ecrit('ips.txt',$myip."\n");
    $N=200;                  
    if (isset($_GET['n'])) {
        $N=$_GET['n'];
    }  
    
    $Country="";                
    if (isset($_GET['c'])) {
        $Country=$_GET['c'];
    }  
    if ($Country == 'none') $Country = 'France';
    if ($Country == '') $Country = 'France';
    
    $MoviesTypes="";       
    if (isset($_GET['g'])) {
        $MoviesTypes=$_GET['g'];
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:2.0.1) Gecko/20100101 Firefox/4.0.1");   
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);                        
    curl_setopt($ch, CURLOPT_REFERER, $myip);     
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    $headers = array();
    $headers[] = 'Accept-Language: fr,fr-FR;q=0.8,en-US;q=0.5,en;q=0.3';    // français
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        /*   DEBBUGGING
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
        */
        
    $param='';    
    if (isset($_GET['t'])){     
        $param=str_replace(' ','+',$_GET['t']);
        $url="http://www.imdb.com/find?q=".urlencode($param)."&s=tt"; 
        curl_setopt($ch, CURLOPT_URL, $url); 
        $body = curl_exec($ch);
        /*   DEBBUGGING
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
        */

        $pattern='#<td class="result_text"> <a href="\/title\/tt((\d){7})\/\?ref_=fn_tt_tt_((\d)*a?)" >(.*?)<\/a>(.*?)<\/td>#';
        $patt2='#.*?\((\d{4})\).*?\((.*?)\).*?#';
        $patt3='#<a href="\/title\/tt((\d){7})\/\?ref_=fn_tt_tt_#';

        $match=array();    
        $RES=[]; 
        if (preg_match_all($pattern,$body,$match)){
            for ($i=0; $i<count($match[0]); $i++){
                array_push($RES,array($match[1][$i],$match[5][$i]));
                $match1=array(); 
                if (preg_match($patt2,$match[6][$i],$match1)){ 
                    if (in_array($match1[2],$Types[0])){
                        $RES[count($RES)-1][2] = $ITypes[$match1[2]];
                    } else {
                        $RES[count($RES)-1][2] = 9;
                    }    
                } else {
                       $RES[count($RES)-1][2] = 0; 
                }
                $match1=array();
                if (preg_match($patt3,$match[6][$i],$match1)){ 
                    $RES[count($RES)-1][3]=$match1[1];
                } else {
                    $RES[count($RES)-1][3]=''; 
                }
            }
        }

        if (count($RES) > 0){
            $List=array();
            for ($c=0; $c<count($RES); $c++){
                if ((pow(2,$RES[$c][2]) & $MoviesTypes) > 0){
                    $i = new Imdb();
                    $imdbUrl = "http://imdb.com/title/tt".$RES[$c][0].'/';
                    $arr=$i->scrapeMovieInfo($imdbUrl, true, $myip, $Country);
                    // internationalization : return french types ! use 0 for english spoken
                    $arr['type']=$Types[1][$RES[$c][2]];
                    $arr['pere']=$RES[$c][3];
                    array_push($List,$arr);   
                }
            }
            if ($List != []){
                $return_val=json_encode($List);
                // filter the apostrophe because Javascript does not appreciate this character ^^
                $List=str_replace("&#x27;", "~~", $return_val);
                echo $List;
            }    
        } else {
            // There is a title but no movie found on IMDb
            echo '0';
        }
    } else {          
        echo "00";  
    } 
?>