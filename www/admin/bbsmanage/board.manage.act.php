<?
include("../class/layout.class");

$db = new Database;

if($act == "get_board_div"){
    $db->query("select div_ix, div_name from bbs_manage_div where bm_ix ='$bm_ix' and disp=1 order by view_order asc");
    $divList = $db->fetchall("object");
    if($db->total > 0){
        $html = "<select name='board_div'>";
        for ($i=0;$i<$db->total;$i++){
            $html .="<option value='".$divList[$i]['div_ix']."'>".$divList[$i]['div_name']."</option>";
        }
        $html .="</select>";
        
	}else{
        $html = "분류 없음";
	}
    echo $html;
	exit;
}


if ($act == "update")
{

	if($board_ename != $bf_board_ename){
		$db->query("select board_ename from bbs_manage_config where board_ename ='$board_ename' ");
		if($db->total){
			echo("<script>alert(\"'$board_ename' 이름의 게시판이 이미 존재합니다 확인후 다시 시도해주세요\");location.href = 'board.manage.list.php?mmode=$mmode';</script>");
			exit;
		}
	}

	if($board_qna_yn == "Y"){
		$board_response_yn = "N";
	}

	$sql = "update bbs_manage_config set board_name='$board_name',board_ename='$board_ename',board_max_cnt='$board_max_cnt',board_titlemax_cnt='$board_titlemax_cnt',design_width='$design_width',
		design_new_priod='$design_new_priod',design_hot_limit='$design_hot_limit',board_searchable='$board_searchable',board_ip_viewable='$board_ip_viewable',board_ip_encoding='$board_ip_encoding',
		board_group='$board_group',board_list_auth='$board_list_auth',board_read_auth='$board_read_auth',board_comment_auth='$board_comment_auth',board_write_auth='$board_write_auth',view_check_yn='$view_check_yn',board_hitcheck_yn = '$board_hitcheck_yn',board_qna_yn='$board_qna_yn',
		view_no_yn='$view_no_yn',view_title_yn='$view_title_yn',view_name_yn='$view_name_yn',view_file_yn='$view_file_yn',view_date_yn='$view_date_yn',view_viewcnt_yn='$view_viewcnt_yn',view_email_yn='$view_email_yn',view_sms_yn='$view_sms_yn',image_click='$image_click',
		break_autowrite='$break_autowrite',break_autocomment='$break_autocomment',board_templete_code='$board_templete_code',bbs_templet_dir='$bbs_templet_dir',
		board_category_use_yn='$board_category_use_yn',board_file_yn='$board_file_yn',board_hidden_yn='$board_hidden_yn',board_response_yn='$board_response_yn',board_comment_yn='$board_comment_yn',board_thumbnail_yn='$board_thumbnail_yn',thum_width='$thum_width',thum_height='$thum_height',board_user_write_auth_yn='$board_user_write_auth_yn',board_admin_write_auth_yn='$board_admin_write_auth_yn',recent_list_display='$recent_list_display',board_point_yn='$board_point_yn',board_point_time='$board_point_time',write_point='$write_point',response_point='$response_point',comment_point='$comment_point', board_recom_yn='$board_recom_yn', view_recommend_yn='$view_recommend_yn', view_comment_yn='$view_comment_yn', view_md_name_yn='$view_md_name_yn', view_read_yn='$view_read_yn',board_seller_write_auth_yn='$board_seller_write_auth_yn',basic_comment_name='$basic_comment_name'
		where bm_ix='$bm_ix' ";//board_recom_yn,view_recommend_yn,view_comment_yn 추가 kbk 13/07/08


	$db->query($sql);
	//exit;
	if($board_ename != $bf_board_ename){
		if($board_style == "bbs"){

			$db->query("ALTER TABLE bbs_".$bf_board_ename." RENAME bbs_".$board_ename) ;
			$db->query("ALTER TABLE bbs_".$bf_board_ename."_comment RENAME bbs_".$board_ename."_comment") ;
		}else if($board_style == "faq"){
			$db->query("ALTER TABLE faq_".$bf_board_ename." RENAME faq_".$board_ename) ;
		}
	}

	echo("<script>location.href = 'board.manage.list.php?mmode=$mmode';</script>");

}

if ($act == "insert")
{
	$db->query("select board_ename from bbs_manage_config where board_ename ='$board_ename' ");

	if($board_qna_yn == "Y"){
		$board_response_yn = "N";
	}
	if(!$db->total){
		$sql = "insert into bbs_manage_config
			(bm_ix,board_name,board_ename,board_max_cnt,bbs_templet_dir,board_style, board_category_use_yn , board_hidden_yn,board_response_yn,board_comment_yn,board_thumbnail_yn,thum_width,thum_height,board_user_write_auth_yn,board_admin_write_auth_yn,recent_list_display,regdate)
			values
			('','$board_name','$board_ename','$board_max_cnt','$bbs_templet_dir','$board_style','$board_category_use_yn','$board_hidden_yn','$board_response_yn','$board_comment_yn','$board_thumbnail_yn','$thum_width','$thum_height','$board_user_write_auth_yn','$board_admin_write_auth_yn','$recent_list_display','$regdate')";

		$sql = "insert into bbs_manage_config(bm_ix,board_name,board_ename,board_max_cnt,board_titlemax_cnt,design_width,design_new_priod,design_hot_limit,board_searchable,board_ip_viewable,board_ip_encoding,board_group,board_list_auth,board_read_auth,board_comment_auth,board_write_auth,view_check_yn,view_no_yn,view_title_yn,view_name_yn,view_file_yn,view_date_yn,view_viewcnt_yn,view_email_yn,view_sms_yn,image_click,break_autowrite,break_autocomment,board_templete_code,bbs_templet_dir,board_style,board_category_use_yn,board_file_yn,board_hidden_yn,board_qna_yn,board_response_yn,board_comment_yn,board_thumbnail_yn,thum_width,thum_height,board_user_write_auth_yn,board_admin_write_auth_yn,recent_list_display,board_point_yn,board_point_time,write_point,response_point,comment_point, regdate,board_recom_yn,view_recommend_yn,view_comment_yn,view_md_name_yn,view_read_yn,board_seller_write_auth_yn,basic_comment_name)
			values
			('','$board_name','$board_ename','$board_max_cnt','$board_titlemax_cnt','$design_width','$design_new_priod','$design_hot_limit','$board_searchable','$board_ip_viewable','$board_ip_encoding','$board_group','$board_list_auth','$board_read_auth','$board_comment_auth','$board_write_auth','$view_check_yn','$view_no_yn','$view_title_yn','$view_name_yn','$view_file_yn','$view_date_yn','$view_viewcnt_yn','$view_email_yn','$view_sms_yn','$image_click','$break_autowrite','$break_autocomment','$board_templete_code','$bbs_templet_dir','$board_style','$board_category_use_yn','$board_file_yn','$board_hidden_yn','$board_qna_yn','$board_response_yn','$board_comment_yn','$board_thumbnail_yn','$thum_width','$thum_height','$board_user_write_auth_yn','$board_admin_write_auth_yn','$recent_list_display','$board_point_yn','$board_point_time','$write_point','$response_point','$comment_point',NOW(),'$board_recom_yn','$view_recommend_yn','$view_comment_yn','$view_md_name_yn','$view_read_yn','$board_seller_write_auth_yn','$basic_comment_name')";//board_recom_yn,view_recommend_yn,view_comment_yn 추가 kbk 13/07/08

		$db->sequences = "BBS_MANAGE_CONFIG_SEQ";
		$db->query($sql);

		if($board_style == "bbs"){
			MakeBoardTable($board_ename,$board_name, $db);
			MakeCommentTable($board_ename,$board_name, $db);
		}else if($board_style == "faq"){
			MakeFAQTable($board_ename,$board_name, $db);
		}

		echo("<script>location.href = 'board.manage.list.php?mmode=$mmode';</script>");
	}else{
		echo("<script>alert(\"'$board_ename' 이름의 게시판이 이미 존재합니다 확인후 다시 시도해주세요\");location.href = 'board.manage.list.php?mmode=$mmode';</script>");
	}

}


if ($act == "delete")
{
	$db->query("select board_ename, board_style  from bbs_manage_config where bm_ix ='".$bm_ix."'");
	$db->fetch();

	//try{
		if($db->dt[board_style] == "bbs"){
			MakeBoardTable($db->dt[board_ename], $db->dt[board_name], $db,"drop");
			MakeCommentTable($db->dt[board_ename], $db->dt[board_name], $db,"drop");
		}else if($board_style == "faq"){
			MakeFAQTable($db->dt[board_ename], $db->dt[board_name], $db,"drop");
		}

	//}catch(Exception $e){};

	$db->query("delete from bbs_manage_config where bm_ix ='".$bm_ix."'");

	echo("<script>location.href = 'board.manage.list.php?mmode=$mmode'</script>");
}



if($act == "vieworder_change"){
//print_r($_POST);
	//print_r($sno);
	//print_r($sort);
	sort($sort);
	//print_r($sort);
	$db = new Database;
	//$db->debug = true;
	for($i=0;$i < count($sno);$i++){
		//$db->query("UPDATE inventory_place_info SET exit_order='".$sort[$i]."' WHERE pi_ix ='".$sno[$i]."' ");
		if($change_all){
			$db->query("UPDATE bbs_manage_config SET vieworder='".($i+1)."' WHERE bm_ix ='".$sno[$i]."' ");
		}else{
			$db->query("UPDATE bbs_manage_config SET vieworder='".$sort[$i]."' WHERE bm_ix ='".$sno[$i]."' ");
		}
	}
//exit;
	echo("<script>parent.document.location.reload();</script>");
	exit;
	
}

function MakeFAQTable($table_name, $table_kname, $mdb, $mode="create"){

	if($mode == "create"){
		/*$sql = "
		CREATE TABLE bbs_".$table_name." (
		  bbs_ix int(8) unsigned zerofill NOT NULL auto_increment,
		  bbs_div int(4)unsigned NULL,
		  sub_bbs_div int(4) unsigned NULL,
		  bbs_q mediumtext NOT NULL,
		  bbs_a mediumtext NOT NULL,
		  bbs_contents_type char(1) NOT NULL default '0',
		  regdate datetime NOT NULL default '0000-00-00 00:00:00',
		  PRIMARY KEY  (bbs_ix)
		) TYPE=InnoDB comment='$table_kname' ";*/

		if($mdb->dbms_type == "oracle"){
			$sql = "
			CREATE TABLE bbs_".$table_name." (
			  bbs_ix NUMBER(10,0) NOT NULL,
			  bbs_div NUMBER(10,0),
			  sub_bbs_div NUMBER(10,0) DEFAULT '0',
			  bbs_q CLOB NOT NULL,
			  bbs_a CLOB NOT NULL,
			  bbs_contents_type CHAR(1 CHAR) DEFAULT '0' NOT NULL,
			  bbs_hit NUMBER(10,0) DEFAULT '0' ,
			  regdate DATE DEFAULT NULL
			)";
			$mdb->query($sql);
			$sql = "
			ALTER TABLE bbs_".$table_name."
			ADD CONSTRAINT PRIMARY_BBS_".$table_name." PRIMARY KEY	(  bbs_ix	)	ENABLE";
			$mdb->query($sql);
			$sql = "
			CREATE SEQUENCE  BBS_".$table_name."_SEQ
			MINVALUE 1 MAXVALUE 999999999999999999999999 INCREMENT BY 1  NOCYCLE";
		}else{
			$sql = "
			CREATE TABLE bbs_".$table_name." (
			  bbs_ix int(8) unsigned zerofill NOT NULL auto_increment COMMENT '게시물 키',
			  bbs_div int(4)unsigned NULL COMMENT '게시물 분류',
			  sub_bbs_div int(4) unsigned NULL COMMENT '게시물 서브 분류',
			  bbs_q mediumtext character set utf8 NOT NULL COMMENT '질문 문의',
			  bbs_a mediumtext character set utf8 NOT NULL COMMENT '질문 대답',
			  bbs_contents_type char(1) character set utf8 NOT NULL default '0' COMMENT '컨텐츠 타입 H : HTML T : Text ',
			  bbs_hit int(8) unsigned default 0 COMMENT '게시물 조회수' ,
			  is_best int(1) unsigned default 0 COMMENT '' ,
			  faq_sort int(5) unsigned default 0 COMMENT '' ,
			  regdate datetime NOT NULL default '0000-00-00 00:00:00' COMMENT '게시물 등록일자',
			  PRIMARY KEY  (bbs_ix)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 comment='$table_kname' ";
		}
echo nl2br($sql);
	}else{
		if($mdb->dbms_type == "oracle"){
			$sql = "drop sequence BBS_".$table_name."_SEQ";
			$mdb->query($sql);
		}

		$sql = "drop table bbs_".$table_name;
	}
	$mdb->query($sql);
}

function MakeBoardTable($table_name,  $table_kname, $mdb, $mode="create"){
	/*
	추천 수 입력받는 필드 추가 bbs_rec_cnt int(4) NOT NULL default '0' COMMENT '추천 수', kbk 13/07/08
	*/
	if($mode == "create"){
		/*$sql =  "
		CREATE TABLE bbs_".$table_name." (
		  bbs_ix int(8) unsigned zerofill NOT NULL auto_increment,
		  bbs_div int(4)unsigned NULL,
		  sub_bbs_div int(4) unsigned NULL,
		  mem_ix varchar(32)  default NULL,
		  bbs_subject varchar(255) NOT NULL default '',
		  bbs_name varchar(50) NOT NULL default '',
		  bbs_pass varchar(50) NOT NULL default '',
		  bbs_email varchar(50) NOT NULL default '',
		  bbs_contents mediumtext NOT NULL,
		  bbs_hidden char(1) NOT NULL default '0',
		  bbs_top_ix int(4) unsigned NOT NULL default '0',
		  bbs_ix_level int(4) unsigned NOT NULL default '0',
		  bbs_ix_step int(4) unsigned NOT NULL default '0',
		  bbs_hit int(8) NOT NULL default '0',
		  bbs_down_cnt   int(8) unsigned NOT NULL default '0',
		  bbs_re_cnt int(4) NOT NULL default '0',
		  bbs_file_1 varchar(255) NOT NULL default '',
		  bbs_file_2 varchar(255) NOT NULL default '',
		  bbs_file_3 varchar(255) NOT NULL default '',
		  bbs_etc1 varchar(255) default NULL,
		  bbs_etc2 varchar(255) default NULL,
		  bbs_etc3 varchar(255) default NULL,
		  bbs_etc4 varchar(255) default NULL,
		  bbs_etc5 varchar(255) default NULL,
		  is_notice enum('Y','N') default 'N',
		  is_html enum('Y','N') default 'N',
		  ip_addr varchar(15) default NULL ,
		  regdate datetime NOT NULL default '0000-00-00 00:00:00',
		  PRIMARY KEY  (bbs_ix)
		) TYPE=InnoDB comment='$table_kname';";*/
		if($mdb->dbms_type == "oracle"){
			$sql = "
			CREATE TABLE bbs_".$table_name." (
			  bbs_ix NUMBER(10,0) NOT NULL,
			  bbs_div NUMBER(10,0),
			  sub_bbs_div NUMBER(10,0),
			  mem_ix VARCHAR2(32 CHAR),
			  bbs_subject VARCHAR2(255 CHAR) NOT NULL,
			  bbs_name VARCHAR2(50 CHAR) NOT NULL,
			  bbs_pass VARCHAR2(50 CHAR),
			  bbs_email VARCHAR2(50 CHAR),
			  bbs_contents CLOB NOT NULL,
			  bbs_hidden CHAR(1 CHAR) DEFAULT '0',
			  bbs_top_ix NUMBER(10,0) DEFAULT '0',
			  bbs_ix_level NUMBER(10,0) DEFAULT '0',
			  bbs_ix_step NUMBER(10,0) DEFAULT '0',
			  bbs_hit NUMBER(10,0) DEFAULT '0',
			  bbs_down_cnt NUMBER(10,0) DEFAULT '0',
			  bbs_re_cnt NUMBER(10,0) DEFAULT '0',
			  bbs_rec_cnt NUMBER(10,0) DEFAULT '0',
			  bbs_file_1 VARCHAR2(255 CHAR),
			  bbs_file_2 VARCHAR2(255 CHAR),
			  bbs_file_3 VARCHAR2(255 CHAR),
			  bbs_etc1 VARCHAR2(255 CHAR),
			  bbs_etc2 VARCHAR2(255 CHAR),
			  bbs_etc3 VARCHAR2(255 CHAR),
			  bbs_etc4 VARCHAR2(255 CHAR),
			  bbs_etc5 VARCHAR2(255 CHAR),
			  is_notice VARCHAR2(4000 CHAR) DEFAULT 'N',
			  is_html VARCHAR2(4000 CHAR) DEFAULT 'N',
			  ip_addr VARCHAR2(15 CHAR),
			  status NUMBER(10,0),
			  regdate DATE DEFAULT NULL
			)";
			$mdb->query($sql);
			$sql = "
			ALTER TABLE bbs_".$table_name."
			ADD CONSTRAINT PRIMARY_BBS_".$table_name." PRIMARY KEY	(  bbs_ix	)	ENABLE";
			$mdb->query($sql);
			$sql = "
			CREATE SEQUENCE  BBS_".$table_name."_SEQ
			MINVALUE 1 MAXVALUE 999999999999999999999999 INCREMENT BY 1  NOCYCLE";
		}else{
			$sql =  "
			CREATE TABLE bbs_".$table_name." (
			  bbs_ix int(8) unsigned zerofill NOT NULL auto_increment COMMENT '게시물 키',
			  bbs_div int(4)unsigned NULL COMMENT '게시물 분류',
			  sub_bbs_div int(4) unsigned NULL COMMENT '게시물 서브 분류',
			  mem_ix varchar(32) character set utf8  default NULL COMMENT '회원키',
			  bbs_subject varchar(255) character set utf8 NOT NULL default '' COMMENT '게시물 제목',
			  bbs_name varchar(50) character set utf8 NOT NULL default '' COMMENT '등록자',
			  bbs_pass varchar(50) character set utf8 NOT NULL default '' COMMENT '게시물 비밀번호 - 비회원시사용',
			  bbs_email varchar(50) character set utf8 NOT NULL default '' COMMENT '이메일',
			  bbs_contents mediumtext character set utf8 NOT NULL COMMENT '게시물 컨텐츠',
			  bbs_hidden char(1) character set utf8 NOT NULL default '0' COMMENT '게시물 공개여부',
			  bbs_top_ix int(4) unsigned NOT NULL default '0' COMMENT '게시물 부모키',
			  bbs_ix_level int(4) unsigned NOT NULL default '0' COMMENT '게시물 레벨',
			  bbs_ix_step int(4) unsigned NOT NULL default '0' COMMENT '게시물 스탭',
			  bbs_hit int(8) NOT NULL default '0' COMMENT '게시물 조회수',
			  bbs_down_cnt   int(8) unsigned NOT NULL default '0' COMMENT '파일 다운로드수',
			  bbs_re_cnt int(4) NOT NULL default '0' COMMENT '컴멘트 수',
			  bbs_rec_cnt int(4) NOT NULL default '0' COMMENT '추천 수',
			  bbs_file_1 varchar(255) character set utf8 NOT NULL default '' COMMENT '업로드 파일 정보 1',
			  bbs_file_2 varchar(255) character set utf8 NOT NULL default '' COMMENT '업로드 파일 정보 2',
			  bbs_file_3 varchar(255) character set utf8 NOT NULL default '' COMMENT '업로드 파일정보3',
			  bbs_etc1 varchar(255) character set utf8 default NULL COMMENT '기타필드1',
			  bbs_etc2 varchar(255) character set utf8 default NULL COMMENT '기타필드2',
			  bbs_etc3 varchar(255) character set utf8 default NULL COMMENT '기타필드3',
			  bbs_etc4 varchar(255) character set utf8 default NULL COMMENT '기타필드4',
			  bbs_etc5 varchar(255) character set utf8 default NULL COMMENT '기타필드5',
			  is_notice enum('Y','N') default 'N' COMMENT ' 공지사항 글 설정',
			  is_html enum('Y','N') default 'N' COMMENT '게시글 html 사용여부',
			  ip_addr varchar(15) character set utf8 default NULL  COMMENT '등록 사용자 IP Address',
			  status INT( 8 ) NULL DEFAULT NULL COMMENT '게시글 상태 ' ,
			  regdate datetime NOT NULL default '0000-00-00 00:00:00' COMMENT '게시글 등록일자',
			  PRIMARY KEY  (bbs_ix)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 comment='$table_kname';";
		}
	}else{
		if($mdb->dbms_type == "oracle"){
			$sql = "drop sequence BBS_".$table_name."_SEQ";
			$mdb->query($sql);
		}

		$sql = "drop table bbs_".$table_name;
	}
	$mdb->query($sql);

}


function MakeCommentTable($table_name, $table_kname, $mdb, $mode="create"){

if($mode == "create"){
	/*$sql =  "CREATE TABLE bbs_".$table_name."_comment (
	  cmt_ix int(8) unsigned zerofill NOT NULL auto_increment,
	  bbs_ix int(8) unsigned zerofill NOT NULL default '00000000',
	  mem_ix varchar(32)  default NULL,
	  cmt_name varchar(50) NOT NULL default '',
	  cmt_pass varchar(50) NOT NULL default '',
	  cmt_email varchar(50) NOT NULL default '',
	  cmt_contents mediumtext NOT NULL,
	  cmt_ip_addr varchar(15) default NULL ,
	  regdate datetime NOT NULL default '0000-00-00 00:00:00',
	  PRIMARY KEY  (cmt_ix)
	) TYPE=InnoDB comment='$table_kname';";*/
	if($mdb->dbms_type == "oracle"){
		$sql =  "CREATE TABLE bbs_".$table_name."_comment (
		  cmt_ix NUMBER(10,0) NOT NULL,
		  bbs_ix NUMBER(10,0) DEFAULT '00000000' NOT NULL,
		  mem_ix VARCHAR2(32 CHAR),
		  cmt_name VARCHAR2(50 CHAR) NOT NULL,
		  cmt_pass VARCHAR2(50 CHAR),
		  cmt_email VARCHAR2(50 CHAR),
		  cmt_contents CLOB NOT NULL,
		  cmt_ip_addr VARCHAR2(15 CHAR),
		  regdate DATE DEFAULT NULL
		)";
		$mdb->query($sql);
		$sql = "
		ALTER TABLE bbs_".$table_name."_comment
		ADD CONSTRAINT PRIMARY_BBS_".$table_name."_COM PRIMARY KEY	(  cmt_ix		)	ENABLE";
		$mdb->query($sql);
		$sql = "
		CREATE SEQUENCE  BBS_".$table_name."_COMMENT_SEQ
		MINVALUE 1 MAXVALUE 999999999999999999999999 INCREMENT BY 1  NOCYCLE";
	}else{
		$sql =  "CREATE TABLE bbs_".$table_name."_comment (
		  cmt_ix int(8) unsigned zerofill NOT NULL auto_increment COMMENT '덧글키',
		  bbs_ix int(8) unsigned zerofill NOT NULL default '00000000' COMMENT '게시글키',
		  mem_ix varchar(32) character set utf8  default NULL COMMENT '등록회원키',
		  cmt_name varchar(50) character set utf8 NOT NULL default '' COMMENT '회원이름',
		  cmt_pass varchar(50) character set utf8 NOT NULL default '' COMMENT '비밀번호',
		  cmt_email varchar(50) character set utf8 NOT NULL default '' COMMENT '이메일',
		  cmt_contents mediumtext character set utf8 NOT NULL COMMENT '덧글내용',
		  cmt_ip_addr varchar(15) character set utf8 default NULL  COMMENT '덧글 등록아이피',
		  regdate datetime NOT NULL default '0000-00-00 00:00:00' COMMENT '덧글 등록일자',
		  PRIMARY KEY  (cmt_ix)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 comment='$table_kname';";
	}
}else{
	if($mdb->dbms_type == "oracle"){
		$sql = "drop sequence BBS_".$table_name."_COMMENT_SEQ";
		$mdb->query($sql);
	}

	$sql = "drop table bbs_".$table_name."_comment";
}
$mdb->query($sql);

}

if($act == "copy_bbs_list")
{

	//선택된 해당 게시판의 idx를 가져온다.
	$origin_bm_ix = explode("|",substr($idx_list,0,strlen($idx_list)-1));
	
	//순서를 바꾸어준다.
	$origin_bm_ix = array_reverse($origin_bm_ix);

	//기존 보드 이름
	$origin_board = $origin_board;

	//복사될 보드이름
	$temp = explode("|",$board_name);
	$des_board_name = $temp[0];
	$des_board_ix = $temp[1];
	if($board_div)
		$des_board_div = $board_div;
	else
		$des_board_div = 0;


	//선택된 갯수만큼 가져온다.
	for($i = 0; $i< count($origin_bm_ix); $i++)
	{
		//bbs_div는 가져온다.
		$sql = "INSERT INTO bbs_".$des_board_name." ( bbs_div, sub_bbs_div, mem_ix, bbs_subject, bbs_name, bbs_pass, bbs_email, bbs_contents, bbs_hidden, bbs_top_ix, bbs_ix_level,
				bbs_ix_step, bbs_hit, bbs_down_cnt, bbs_re_cnt, bbs_file_1, bbs_file_2, bbs_file_3, bbs_etc1, bbs_etc2, bbs_etc3, bbs_etc4,
				bbs_etc5, is_notice, is_html, ip_addr, status, regdate)
				(SELECT ".$des_board_div.", sub_bbs_div, mem_ix, bbs_subject, bbs_name, bbs_pass, bbs_email, bbs_contents, bbs_hidden, 
				(SELECT MAX(bbs_top_ix) + 1 as bbs_top_ix FROM bbs_".$des_board_name.") , 
				bbs_ix_level, bbs_ix_step, bbs_hit, bbs_down_cnt, bbs_re_cnt, bbs_file_1, bbs_file_2, bbs_file_3, bbs_etc1, bbs_etc2, bbs_etc3, bbs_etc4,
				bbs_etc5, 'N', is_html, ip_addr, status, NOW() FROM bbs_".$origin_board." o WHERE bbs_ix = ".$origin_bm_ix[$i].")";	
		$db->query($sql);
		
		//새로 등록된 bbs_ix를 가져온다.
		$sql = "SELECT bbs_ix FROM  bbs_".$des_board_name." WHERE bbs_ix = LAST_INSERT_ID()";
		$db->query($sql);
		$db->fetch();
		$new_bbs_ix = $db->dt[bbs_ix];

		if($des_board_name == "after" || $des_board_name == "premium_after"){
			$pid = str_pad($pid, 10, '0', STR_PAD_LEFT);

			$sql = "update bbs_".$des_board_name." set
						bbs_etc1 = '".$pid."'
						,bbs_etc2 = (select pname from shop_product where id='".$pid."')
						,bbs_etc5 = (select admin from shop_product where id='".$pid."')
					WHERE bbs_ix = '".$new_bbs_ix."' ";
			$db->query($sql);
		}
		
		$new_bbs_ix = str_pad($new_bbs_ix,8,'0',STR_PAD_LEFT);

		//파일을 복사해야한다.
		//폴더를 생성해준다.
		if(!file_exists($_SERVER['DOCUMENT_ROOT'].$layout_config["mall_data_root"]."/bbs_data/bbs_".$des_board_name."/")){
			mkdir($_SERVER['DOCUMENT_ROOT'].$layout_config["mall_data_root"]."/bbs_data/bbs_".$des_board_name."/");
			chmod($_SERVER['DOCUMENT_ROOT'].$layout_config["mall_data_root"]."/bbs_data/bbs_".$des_board_name."/",0777);
		}

		if(!file_exists($_SERVER['DOCUMENT_ROOT'].$layout_config["mall_data_root"]."/bbs_data/bbs_".$des_board_name."/$new_bbs_ix")){
			mkdir($_SERVER['DOCUMENT_ROOT'].$layout_config["mall_data_root"]."/bbs_data/bbs_".$des_board_name."/$new_bbs_ix");
			chmod($_SERVER['DOCUMENT_ROOT'].$layout_config["mall_data_root"]."/bbs_data/bbs_".$des_board_name."/$new_bbs_ix",0777);
		}
		
		$destination_directory = $_SERVER['DOCUMENT_ROOT'].$layout_config["mall_data_root"]."/bbs_data/bbs_".$des_board_name."/".$new_bbs_ix;
		$source_directory = $_SERVER['DOCUMENT_ROOT'].$layout_config["mall_data_root"]."/bbs_data/bbs_".$origin_board."/".$origin_bm_ix[$i];
		
		//복사할 게시판의 데이터를 접근해서 복사해준다

		//디렉토리가 존재하면
		if(is_dir($source_directory))
		{
			//디렉토리를 열어서
			if($dir = opendir($source_directory))
			{
				//파일을 읽고
				while( ($file = readdir($dir)) !== false)
				{
					if($file[0] != ".")
					{	
						//파일 복사
						copy($source_directory."/".$file, $destination_directory."/".$file);
					}
				}
			}
		}
	}

	echo "<script>alert('정상적으로 복사되었습니다.');top.document.getElementById('copy_bbs').style.visibility = 'hidden';top.document.location.reload();</script>";
}


if($act == 'set_mile'){

	if($bbs_ix == ''){
		echo "<script>alert('게시물을 선택해주세요');self.close();</script>";
	}

	if($reserve_input == ''){
		echo "<script>alert('마일리지 금액이 설정되지 않았습니다');self.close();</script>";
	}

	if($board == 'after'){
		$board_name = '일반';
		$board_type = 'bbs_after';
	}else if($board == 'premium_after'){
		$board_name = '프리미엄';
		$board_type = 'bbs_premium_after';
	}

	$bbs_array = explode(',', $bbs_ix);
	$bbs_ix_str = implode("','", $bbs_array);

	$sql = "SELECT mem_ix, bbs_ix, bbs_name FROM ".$board_type." WHERE bbs_ix in ('".$bbs_ix_str."')";	
	$db->query($sql);
	$user_datas = $db->fetchall("object");

	for($k=0; $k<count($user_datas); $k++){

		$sql = "select * from shop_reserve_info where od_ix='".$user_datas[$k][bbs_ix]."' and uid='".$user_datas[$k][mem_ix]."'";	
		$db->query($sql);
		
		if($db->total > 0){
		} else{
			//echo $reserve_input.'<br>';
			//echo $user_datas[$k][mem_ix].'<br>';
			//echo $user_datas[$k][bbs_ix].'<br>';
			//echo $reserve_input.'<br>';

			InsertReserveInfo($user_datas[$k][mem_ix],$board_type,$user_datas[$k][bbs_ix],'',$reserve_input,'1','5',$board_name." 후기 쓰기에 대한 마일리지",'mileage',$admininfo);

			//New 마일리지 시스템 JK160323
			InsertMileageInfo($user_datas[$k][mem_ix],'7',$reserve_input,$board_name." 후기 쓰기에 대한 마일리지",'add',$board_type,$user_datas[$k][bbs_ix]);

			/*신규 포인트,마일리지 접립 함수 JK 160405*/
			$mileage_data[uid] = $user_datas[$k][mem_ix];
			$mileage_data[type] = 7;
			$mileage_data[mileage] = $reserve_input;
			$mileage_data[message] = $board_name." 후기 쓰기에 대한 마일리지";
			$mileage_data[state_type] = 'add';
			$mileage_data[save_type] = 'mileage';
			$mileage_data[oid] = $board_type;
			$mileage_data[od_ix] = $user_datas[$k][bbs_ix];
			InsertMileageInfo($mileage_data);
		}
	}
	//exit;

/*
	$alert_mile .= "<script>alert('적립금을 지급하였습니다\\n";

		for($j=0; $j<count($user_datas); $j++){
			$alert_mile .= $user_datas[$j][bbs_name].': '.$user_datas[$j][bbs_ix].'\\n';
		}
	
	$alert_mile .=">');self.close();</script>";

	echo $alert_mile;
*/
	echo "<script>alert('마일리지를 지급하였습니다');self.close();</script>";

}


/*
alter table bbs_manage_config add board_titlemax_cnt int(3) default 20 after board_max_cnt; -- 제목글자수 제한
alter table bbs_manage_config add design_width varchar(10) default '100%' after board_titlemax_cnt; -- 게시판 넓이
alter table bbs_manage_config add design_new_priod int(3) default 24 after design_width; -- NEW 아이콘 효력 시간
alter table bbs_manage_config add design_hot_limit int(3) default 50 after design_new_priod; -- HOT 아이콘 제한
alter table bbs_manage_config add board_searchable enum('0','1') default '1' after design_hot_limit; -- 통합검색 노출여부
alter table bbs_manage_config add board_ip_viewable enum('0','1') default '1' after board_searchable; -- IP 노출여부
alter table bbs_manage_config add board_ip_encoding enum('0','1') default '1' after board_ip_viewable; -- IP 암호화 여부
alter table bbs_manage_config add board_group enum('H','C','G') default 'H' after board_ip_encoding; -- 게시판 그룹 H (help):  고객센타 , C(community) : 커뮤니티, G(general) : 일반게시판
alter table bbs_manage_config add board_list_auth int(2) default '1' after board_group; -- 리스트 보기 사용자 권한
alter table bbs_manage_config add board_read_auth int(2) default '1' after board_list_auth ; -- 읽기 사용자 권한
alter table bbs_manage_config add board_comment_auth int(2) default '1' after board_read_auth  ; -- 콤멘트 쓰기 사용자 권한
alter table bbs_manage_config add board_write_auth int(2) default '1' after board_comment_auth ; -- 쓰기 사용자권한

alter table bbs_manage_config add view_check_yn enum('0','1') default '1' after board_write_auth ;  -- 리스트에  체크박스 노출여부
alter table bbs_manage_config add view_no_yn enum('0','1') default '1' after view_check_yn ;  -- 리스트에 넘버 노출여부
alter table bbs_manage_config add view_title_yn enum('0','1') default '1' after view_no_yn ;   -- 리스트에 제목 노출여부
alter table bbs_manage_config add view_name_yn enum('0','1') default '1' after view_title_yn  ;  -- 리스트에 이름 노출여부
alter table bbs_manage_config add view_file_yn enum('0','1') default '1' after view_name_yn ;   -- 리스트에 파일 노출여부
alter table bbs_manage_config add view_date_yn enum('0','1') default '1' after view_file_yn  ;   -- 리스트에 날짜 노출여부
alter table bbs_manage_config add view_viewcnt_yn enum('0','1') default '1' after view_date_yn;   -- 리스트에 조회수 노출여부

alter table bbs_manage_config add image_click enum('V','P','LP') default 'V' after view_viewcnt_yn;  -- 이미지 클릭시 액션 여부 V : 읽기 페이지로 이동, P :  팝업 , LP : 레이어 팝업
alter table bbs_manage_config add break_autowrite enum('0','1') default '0' after image_click;   -- 자동글쓰기 방지 기능
alter table bbs_manage_config add break_autocomment enum('0','1') default '0' after break_autowrite;  -- 자동 컴멘트 달기 방지 기능

*/
?>
