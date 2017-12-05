<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        function get_file_extension($file_name) {
            return substr(strrchr($file_name,'.'),1,3);
        }

        $url = "https://originative.co/";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_REFERER, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201');
        $str = curl_exec($curl);
        curl_close($curl);

        $dom = new domDocument;
        #libxml_use_internal_errors(true);
        @$dom->loadHTML($str);

        #get links
        foreach($dom->getElementsByTagName('a') as $link) {
            # Show the <a href>
            $templink = $link->getAttribute('href');
            $linktype = get_file_extension($templink);
            if (!empty($linktype)) {
                echo $templink . "------" . $linktype;
                echo "<br />";
            }
        }
        echo "<br />";

        #get images
        foreach($dom->getElementsByTagName('img') as $image) {
            $tempimage = $image->getAttribute('src');
            $imagetype = get_file_extension($tempimage);
            echo $tempimage . "------" . $imagetype;
            echo "<br />";
        }
        ?>
    </body>
</html>
