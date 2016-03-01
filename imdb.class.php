<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////////
//                 Adapted from :
// Free PHP IMDb Scraper API for the new IMDb Template.
// Version: 4.4
// Author: Abhinay Rathore
// Website: http://www.AbhinayRathore.com
// Blog: http://web3o.blogspot.com
// Demo: http://lab.abhinayrathore.com/imdb/
// More Info: http://web3o.blogspot.com/2010/10/php-imdb-scraper-for-new-imdb-template.html
// Last Updated: May 6, 2014
/////////////////////////////////////////////////////////////////////////////////////////////////////////
//  https://github.com/abhinayrathore/PHP-IMDb-Scraper
///////////////////////////////////////////////////////////////////////////////////////////////////////// 
class Imdb
{   
    // Scrape movie information from IMDb page and return results in an array.
    // this function becomes public by ArouG and has been modified !
    // I prefer use the IMDb search engine and not ASK, GOOGLE and the others ! Much better ^^
    public function scrapeMovieInfo($imdbUrl, $getExtraInfo = true, $myip, $Country = '')
    {
        require('langages.php');
        $arr = array();
        $html = $this->geturl("${imdbUrl}combined",  $myip);
        $title_id = $this->match('/<link rel="canonical" href="http:\/\/www.imdb.com\/title\/(tt\d+)\/combined" \/>/ms', $html, 1);
        if(empty($title_id) || !preg_match("/tt\d+/i", $title_id)) {
            $arr['error'] = "No Title found on IMDb!";
            return $arr;
        }
        $arr['imdb_id'] = $title_id;
        $tmp= str_replace('"', '', trim($this->match('/<title>(IMDb \- )*(.*?) \(.*?<\/title>/ms', $html, 2)));
        $arr['title'] = html_entity_decode($tmp, ENT_COMPAT, 'UTF-8');
        $arr['year'] = trim($this->match('/<title>.*?\(.*?(\d{4}).*?\).*?<\/title>/ms', $html, 1));

        $arr['genres'] = $this->match_all('/\/Sections\/Genres\/(.*?)\//ms', $this->match('/<h5>Genre:<\/h5>(.*?)<\/div>/ms',$html,0), 1);
            // assures $arr['genres'] is a string ! If no genres, $arr['genres']=''
            if (count($arr['genres']) > 0){
                $tmp=$Imdb_genres[1][$IImdb_genres[$arr['genres'][0]]];
                for ($i=1; $i < count($arr['genres']); $i++){
                    // internationalization : return french genres ! use 0 for english spoken
                    $tmp .= '|'.$Imdb_genres[1][$IImdb_genres[$arr['genres'][$i]]];
                }
            } else $tmp = ''; 
        $arr['genres']=$tmp;           

        $arr['directors'] = $this->match_all_key_value('/<td valign="top"><a.*?href="\/name\/(.*?)\/">(.*?)<\/a>/ms', $this->match('/Directed by<\/a><\/h5>(.*?)<\/table>/ms', $html, 1)); 
            // assures $arr['directors'] is a string ! If no directors (???), $arr['directors']=''   
            if (count($arr['directors']) > 0){
                $arr['realisateurs']=array_values($arr['directors']);
                $tmp=$arr['realisateurs'][0];
                for ($i=1; $i < count($arr['realisateurs']); $i++){
                    $tmp .= ' | '.$arr['realisateurs'][$i];
                }
            } else $tmp = '';
            unset($arr['realisateurs']);
            $arr['directors'] = html_entity_decode($tmp, ENT_COMPAT, 'UTF-8');

        $arr['poster'] = $this->match('/<div class="photo">.*?<a name="poster".*?><img.*?src="(.*?)".*?<\/div>/ms', $html, 1);
            // assures $arr['poster'] is a string AND $arr['poster'] is the BIGGEST image !   
            $arr['poster_large'] = "";
            $arr['poster_full'] = "";
            if ($arr['poster'] != '' && strpos($arr['poster'], "media-imdb.com") > 0) { //Get large and small posters
                $arr['poster'] = preg_replace('/_V1.*?.jpg/ms', "_V1._SY200.jpg", $arr['poster']);
                $arr['poster_large'] = preg_replace('/_V1.*?.jpg/ms', "_V1._SY500.jpg", $arr['poster']);
                $arr['poster_full'] = preg_replace('/_V1.*?.jpg/ms', "_V1._SY0.jpg", $arr['poster']);
                if ($arr['poster_full'] != ''){
                    $arr['poster'] = $arr['poster_full'];
                    unset($arr['poster_full']);
                    unset($arr['poster_large']);
                } else {
                    if ($arr['poster_large'] != ''){
                        $arr['poster'] = $arr['poster_large'];
                        unset($arr['poster_full']);
                        unset($arr['poster_large']);
                    }
                }
            } else {
                $arr['poster'] = "";
            }

        $arr['country'] = $this->match_all('/<a.*?>(.*?)<\/a>/ms', $this->match('/Country:(.*?)(<\/div>|>.?and )/ms', $html, 1), 1);
            // assures $arr['country'] is a string ! If no country (???), $arr['country']=''   
            if (count($arr['country']) > 0){
                $tmp=$arr['country'][0];
                for ($i=1; $i < count($arr['country']); $i++){
                    $tmp .= ' | '.$arr['country'][$i];
                }
            } else $tmp = '';
            $arr['country'] = html_entity_decode($tmp, ENT_COMPAT, 'UTF-8');
             
        if($getExtraInfo == true) {
            $releaseinfoHtml = $this->geturl("http://www.imdb.com/title/" . $arr['imdb_id'] . "/releaseinfo",  $myip);
            $arr['also_known_as'] = $this->getAkaTitles($releaseinfoHtml);   
                
            // pre-treatment because IMDB.com output depends on the country of client ! Here, the server is in Germany. So add Original Title if the country is Germany
            // prétraitement lié au fait que le serveur est "allemand" - ajouter Original title si country = west germany ou germany
            if (strpos($arr['country'],'Germany') !== false){
                // on rajoute titre allemand ! We add in AKAs german title with original title
                $arr['also_known_as'][]=$arr['title']." [Germany (original title)]";    
            }
            $max=count($arr['also_known_as']);
            if ($Country != 'all'){
                for ($i=0; $i<$max; $i++){
                        if ($Country == 'France'){
                            // I know the langage from France (french)
                            $a1=preg_match('/France/i',$arr['also_known_as'][$i]);
                            $a3=preg_match('/French title/i',$arr['also_known_as'][$i]);
                        } else {
                            // here, $a3 should match the english langage from USA / UK etc ... depend on $Country
                            $a1=preg_match("/".$Country."/i",$arr['also_known_as'][$i]);
                            $a3=0;
                        }
                        $a2=preg_match('/original title/i',$arr['also_known_as'][$i]);
                        $a4=preg_match('/World-wide/i',$arr['also_known_as'][$i]);
                        $a5=$a1+$a2+$a3+$a4;
                        if ($a5 == 0){
                            unset($arr['also_known_as'][$i]);
                        } else {
                            if ($a1 == 1) {
                                $tmpmatch=array();
                                $tmpReg='#(.*?)\s+\[(.*?)'.$Country.'(.*?)\]#';
                                $tmp=preg_match($tmpReg,$arr['also_known_as'][$i],$tmpmatch);
                                $arr['title']=$tmpmatch[1];
                                unset($arr['also_known_as'][$i]);
                            } else {
                                if (($a2 == 1) && (strpos($arr['country'],$Country) !== false)){
                                    $tmpmatch=array();
                                    $tmpReg='#(.*?)\s+\[(.*?)'.'original title'.'(.*?)\]#';
                                    $tmp=preg_match($tmpReg,$arr['also_known_as'][$i],$tmpmatch);
                                    $arr['title']=$tmpmatch[1];
                                    unset($arr['also_known_as'][$i]);
                                }
                            }   
                        }
                }  
            }
            $arr['also_known_as']=array_values($arr['also_known_as']);        
            if (count($arr['also_known_as']) > 0){
                $tmp=$arr['also_known_as'][0];
                for ($i=1; $i < count($arr['also_known_as']); $i++){
                    $tmp .= ', '.$arr['also_known_as'][$i];
                }
            } else $tmp = '';         
            $arr['also_known_as'] = html_entity_decode($tmp, ENT_COMPAT, 'UTF-8');
        } else {
            $arr['also_known_as'] = [];    
        }         

        $arr['videos'] = $this->getVideos($arr['title_id']);
            if (count($arr['videos']) > 0){
                $tmp=$arr['videos'][0];
                for ($i=1; $i < count($arr['videos']); $i++){
                    $tmp .= ' | '.$arr['videos'][$i];
                }
            } else $tmp = '';
            $arr['videos'] = html_entity_decode($tmp, ENT_COMPAT, 'UTF-8');
               
        return $arr;
    }

    private function getAkaTitles($html){
        $akaTitles = array();
        foreach($this->match_all('/<tr.*?>(.*?)<\/tr>/msi', $this->match('/<table id="akas".*?>(.*?)<\/table>/ms', $html, 1), 1) as $m) {
            $akaTitleMatch = $this->match_all('/<td>(.*?)<\/td>/ms', $m, 1);
            $akaCountry = trim($akaTitleMatch[0]);
            $akaTitle = trim($akaTitleMatch[1]);
            array_push($akaTitles, $akaTitle . " [" . $akaCountry.  "]");
        }       
        return array_filter($akaTitles);
    }

    public function getVideos($titleId){
        $html = $this->geturl("http://www.imdb.com/title/${titleId}/videogallery", $myip);
        $videos = array();
        foreach ($this->match_all('/<a.*?href="(\/video\/imdb\/.*?)".*?>.*?<\/a>/ms', $html, 1) as $v) {
            $videos[] = "http://www.imdb.com${v}";
        }
        return array_filter($videos);
    }

    private function geturl($url, $myip){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);   
        // try to fake Imdb.com ( I won !)
        curl_setopt($ch, CURLOPT_REFERER, $myip); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        /*   DEBBUGGING
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
        */
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
        // try to fake Imdb.com ( I won !)
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("REMOTE_ADDR: $myip", "HTTP_X_FORWARDED_FOR: $myip"));
        //curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/".rand(3,5).".".rand(0,3)." (Windows NT ".rand(3,5).".".rand(0,2)."; rv:2.0.1) Gecko/20100101 Firefox/".rand(3,5).".0.1");
        $headers = array();
        $headers[] = 'Accept-Language: fr,fr-FR;q=0.8,en-US;q=0.5,en;q=0.3';    // french langage but I won again !
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $html = curl_exec($ch);
        curl_close($ch);
        /*   DEBBUGGING
        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
        */
        return $html;
    }
 
    private function match_all_key_value($regex, $str, $keyIndex = 1, $valueIndex = 2){
        $arr = array();
        preg_match_all($regex, $str, $matches, PREG_SET_ORDER);
        foreach($matches as $m){
            $arr[$m[$keyIndex]] = $m[$valueIndex];
        }
        return $arr;
    }
     
    private function match_all($regex, $str, $i = 0){
        if(preg_match_all($regex, $str, $matches) === false)
            return false;
        else
            return $matches[$i];
    }
 
    private function match($regex, $str, $i = 0){
        if(preg_match($regex, $str, $match) == 1)
            return $match[$i];
        else
            return false;
    }
}
?>