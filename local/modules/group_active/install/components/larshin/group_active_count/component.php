<?
\Bitrix\Main\Loader::includeModule('iblock');

if($_REQUEST['clear']!='yes'){//кэширование по времени заданному в параметрах, так как сложн оотследить изменение пользователей, то массив опций останется пустым
	$time=0;//выводим без кэша при стандартном сбросе
}else{
	if($arParams['TIME']){
		$time=$arParams['TIME'];//если параметр кэша хадан, то считаывем его
	}else{
		$time=360000;//иначе стандартное время жизни кэша
	}
}
function active_count($DBResult){
	$result['ITEM']=array();
	while ($arGroup = $DBResult->fetch()) 
	{
		if(!$result['ITEM'][$arGroup['GROUP_ID']]){//проверяем есть ли эта группа в списке
			$result['ITEM'][$arGroup['GROUP_ID']]=array('NAME'=>$arGroup['MAIN_USER_GROUP_GROUP_NAME'],'COUNT'=>0);// если гурппа новая ,то создаем ее
		}
		if($arGroup['MAIN_USER_GROUP_USER_ACTIVE']=="Y")
			$result['ITEM'][$arGroup['GROUP_ID']]['COUNT']++;//если есть активный пользователь,то добавдляем его
	}
	return $result['ITEM'];
}

$arError='';//здесь не используется , но почти всегда необходим при масштабировании-отключаем выполнения кэша при ошибка
if($this->StartResultCache($time, array())){ 
	$arResult=array();
	$arResult['ITEM']=array();
	$arResult['PARAM']=$arParams;

	$result=\Bitrix\Main\UserGroupTable::getList(array(//получаем и неактивных пользовыателей, дабы с 0 значением поля также отображались
		'order'=>array('GROUP_ID'),
		'filter' => array(),
		'select' => array('GROUP_ID','USER.EMAIL',"GROUP",'USER.ACTIVE'), // емаил для контроля  и отладки-наглядность-можно убрать
	));
	$arResult['ITEM']=active_count($result);
	
	$this->IncludeComponentTemplate();
	//шаблон загружен в кэш
	if($arError)
	{
		$this->AbortResultCache();
		ShowError("ERROR");
		@define("ERROR_404", "Y");
		if($arParams["SET_STATUS_404"]==="Y")
			CHTTP::SetStatus("404 Not Found");
	}
}else{
	//echo 'Ўаблон вз€т и кеша!<BR>';// происходит тогда, когда загружен кеш-эффективно дл€ проверки работы кеша и скорости без него!!
}
?>