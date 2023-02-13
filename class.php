<?

$link = 'https://youtu.be/homqyBxHwis';

class myClass {
   
	private $link_resourse;
   
	public function __construct($link_resourse) {
        $this->link_resourse = trim($link_resourse);
	}

	/*Получаем массив элементов из ссылки*/

	public function getArrayLink(){
		
		$array_url = parse_url(trim($this->link_resourse)); 

		return $array_url;
	}

	/*Получаем id видео если присутствуют значения из get параметров*/

	public function getIdVideoForQuery($query){
		
		$arQuery = parse_str($query, $arVarsQuery);

		$id_elem = array_shift($arVarsQuery);
		
		return $id_elem;

	}

	/*Получаем id из url если ссылки get параметров */

	public function getIdVideo(){
		
		$arrayUrl = self::getArrayLink();

		if($arrayUrl["query"]) {
			$id_elem = self::getIdVideoForQuery($arrayUrl['query']);
			
			return $id_elem;
		}

		$id_elem = str_replace('/', '', $arrayUrl['path']);

		return $id_elem;

	}

	/*Получаем содержимое страницы по текущему url*/

	public function GetContent($url){
		
		$c = curl_init($url);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		$html = curl_exec($c);
		curl_close($c);

		return $html;
	}

	/*Получаем название хостинга */

	public function getNameHosting() {

		$link_embed = self::generateVideoEmbedUrl($this->link_resourse);

		$arInfo = self::GetContent($link_embed);

		preg_match("/<title>(.*)<\/title>/siU",$arInfo,$title_matches);

		if(is_array($title_matches)) {
			foreach($title_matches as $title) {
				
				if($title) {
					$name = strip_tags($title);
					return $name;
				}
			}
		}
		return false;

	}

	/*Генерируем ссылки для iframe чтобы корректно вытянуть содержимое*/

	public function generateVideoEmbedUrl($url){

		$finalUrl = '';
		if(strpos($url, 'vimeo.com/') !== false) {

			$videoId = explode("vimeo.com/",$url)[1];
			if(strpos($videoId, '&') !== false){
				$videoId = explode("&",$videoId)[0];
			}
			$finalUrl.='https://player.vimeo.com/video/'.$videoId;

		}else if(strpos($url, 'youtube.com/') !== false) {

			$videoId = explode("v=",$url)[1];
			if(strpos($videoId, '&') !== false){
				$videoId = explode("&",$videoId)[0];
			}
			$finalUrl.='https://www.youtube.com/embed/'.$videoId;

		}else if(strpos($url, 'youtu.be/') !== false){

			$videoId = explode("youtu.be/",$url)[1];
			if(strpos($videoId, '&') !== false){
				$videoId = explode("&",$videoId)[0];
			}
			$finalUrl.='https://www.youtube.com/embed/'.$videoId;
		}

	 return $finalUrl;

	}

	/*Создаем Iframe на основе полученных ссылок*/

	public function getIframe() {
	
		$link_video = self::generateVideoEmbedUrl($this->link_resourse);
		
		if($link_video) {
		
			$iframe = '<iframe width="500" height="500" src='.$link_video.'></iframe>';

			return $iframe;
		}

		return false;
	}

	/*Выводим всю полученную информацию */

	public function showInfo() {
		
		echo('Id видео: '.self::getIdVideo().'<br>');
		
		echo('Название видеохостинга'.self::getNameHosting().'<br>');

		echo('Iframe: '.self::getIframe().'<br>');
	} 

}


/*Создаем новый объект класса и вызываем методы этого класса*/

$info = new myClass($link);

$info->showInfo();
	

	
?>