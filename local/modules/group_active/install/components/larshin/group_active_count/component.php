<?
\Bitrix\Main\Loader::includeModule('iblock');

if($_REQUEST['clear']!='yes'){//����������� �� ������� ��������� � ����������, ��� ��� ����� ���������� ��������� �������������, �� ������ ����� ��������� ������
	$time=0;//������� ��� ���� ��� ����������� ������
}else{
	if($arParams['TIME']){
		$time=$arParams['TIME'];//���� �������� ���� �����, �� ��������� ���
	}else{
		$time=360000;//����� ����������� ����� ����� ����
	}
}
function active_count($DBResult){
	$result['ITEM']=array();
	while ($arGroup = $DBResult->fetch()) 
	{
		if(!$result['ITEM'][$arGroup['GROUP_ID']]){//��������� ���� �� ��� ������ � ������
			$result['ITEM'][$arGroup['GROUP_ID']]=array('NAME'=>$arGroup['MAIN_USER_GROUP_GROUP_NAME'],'COUNT'=>0);// ���� ������ ����� ,�� ������� ��
		}
		if($arGroup['MAIN_USER_GROUP_USER_ACTIVE']=="Y")
			$result['ITEM'][$arGroup['GROUP_ID']]['COUNT']++;//���� ���� �������� ������������,�� ���������� ���
	}
	return $result['ITEM'];
}

$arError='';//����� �� ������������ , �� ����� ������ ��������� ��� ���������������-��������� ���������� ���� ��� ������
if($this->StartResultCache($time, array())){ 
	$arResult=array();
	$arResult['ITEM']=array();
	$arResult['PARAM']=$arParams;

	$result=\Bitrix\Main\UserGroupTable::getList(array(//�������� � ���������� ��������������, ���� � 0 ��������� ���� ����� ������������
		'order'=>array('GROUP_ID'),
		'filter' => array(),
		'select' => array('GROUP_ID','USER.EMAIL',"GROUP",'USER.ACTIVE'), // ����� ��� ��������  � �������-�����������-����� ������
	));
	$arResult['ITEM']=active_count($result);
	
	$this->IncludeComponentTemplate();
	//������ �������� � ���
	if($arError)
	{
		$this->AbortResultCache();
		ShowError("ERROR");
		@define("ERROR_404", "Y");
		if($arParams["SET_STATUS_404"]==="Y")
			CHTTP::SetStatus("404 Not Found");
	}
}else{
	//echo '������ ���� � ����!<BR>';// ���������� �����, ����� �������� ���-���������� ��� �������� ������ ���� � �������� ��� ����!!
}
?>