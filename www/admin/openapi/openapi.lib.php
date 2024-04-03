<?
/**
 * 오픈마켓 통합 클래스
 * 
 * @last date 2013.11.25
 * @author bgh
 */
include_once $_SERVER ['DOCUMENT_ROOT'] . '/class/database.class';
//require_once $_SERVER ['DOCUMENT_ROOT'] . '/admin/openapi/cjmall/cjmall.lib.php';
//require_once $_SERVER ['DOCUMENT_ROOT'] . '/admin/openapi/gsshop/gsshop.lib.php';
//require_once $_SERVER ['DOCUMENT_ROOT'] . '/admin/openapi/demandship/demandship.lib.php';
require_once $_SERVER ['DOCUMENT_ROOT'] . '/admin/openapi/npay/npay.lib.php';
require_once $_SERVER ['DOCUMENT_ROOT'] . '/admin/openapi/goodsflow/goodsflow.lib.php';

ini_set('include_path', '.:/usr/local/lib/php:' . $_SERVER['DOCUMENT_ROOT'] . '/include/pear');
$install_path = '../../include/';
include_once('SOAP/Client.php');

class OpenAPI{	
	public $lib;
	private $db;
	private $site_code;

	public function __construct($site_code){
		if(empty ($site_code)){
			echo '<script>alert("제휴사 선택이 필요합니다");</script>';
			exit;
		}
		$this->db = new Database();
		$siteinfo = $this->getSiteByKey($site_code);
		
		$this->site_code = $siteinfo['site_code'];

		switch($this->site_code){
			case '11st':
				$this->lib = new Lib_11st($site_code);
				break;
			case 'goodss':
			case 'isoda':
				$this->lib = new lib_goodss($this->site_code);
				break;
			case 'auction':
				$this->lib = new Lib_auction($site_code);
				break;
			case 'ESM':
				$this->lib = new Lib_esm($site_code);
				break;
			case 'storyway':
				$this->lib = new Lib_storyway($site_code);
				break;
			case 'halfclub':
				$this->lib = new Lib_halfclub($site_code);
				break;
			case 'gmarket':
				$this->lib = new Lib_gmarket($site_code);
				break;
			case 'fashionplus':
				$this->lib = new Lib_fashionplus($site_code);
				break;
			case 'cjmall':
				$this->lib = new Lib_cjmall($site_code);
				break;
			case 'gsshop':
				$this->lib = new Lib_gsshop($site_code);
				break;
			case 'interpark_api':
			case 'interpark':
				$this->lib = new Lib_interpark($site_code);
				break;
			case 'lazada':
				$this->lib = new Lib_lazada($site_code);
				break;
			case 'lazada_v2':
				$this->lib = new Lib_lazada_v2($site_code);
				break;
			case 'qoo10':
				$this->lib = new Lib_qoo10($site_code);
				break;
			case 'demandship':
				$this->lib = new Lib_demandship($site_code);
				break;
            case 'goodsflow':
                $this->lib = new Lib_goodsflow($site_code);
                break;
            case 'npay':
                $this->lib = new Lib_npay($site_code);
                break;
		}
	}
	
	/**
	 * /**
	 * 키값으로 사이트코드를 구한다.
	 * 
	 * @param string $api_key
	 * @return array|null
	 */
	private function getSiteByKey($site_code){
		$sql = "SELECT
					* 
				FROM 
					sellertool_site_info
				WHERE
					site_code = '".$site_code."'";
		$this->db->query($sql);
		if($this->db->total){
			return $this->db->fetch();
		}
		return null;
	}
	
	/**
	 * 사이트코드
	 */
	public function getSiteCode(){
		return $this->site_code;
	}
	
	public function searchCategory($cname = "") {
		$sql = "SELECT
           			depth,disp_name,disp_no,parent_no
           		FROM sellertool_received_category
           		WHERE
           			site_code = '".$this->site_code."' ";
		 
			$sql .= "AND
					disp_name LIKE '%" . $cname . "%' ";
	 
		$sql .= "ORDER BY disp_no asc , disp_name ASC limit 200 ";
		$this->db->query ( $sql );
		if ($this->db->total) {
			$result = $this->db->fetchall ( "object", MYSQL_ASSOC );
			$key = 0;
			foreach ( $result as $rt ) :
				$return [$key] = new categoryData ();
				$return [$key]->depth = $rt ['depth'];
				$return [$key]->disp_name = $rt ['disp_name']."(".$rt ['depth'].")";
				$return [$key]->category_path = getSellertoolReceivedCategoryPathByAdmin($rt ['disp_no'], $this->site_code);
				$return [$key]->disp_no = $rt ['disp_no'];
				$return [$key]->parent_no = $rt ['parent_no'];
				
				$key ++;
			endforeach
			;
		} else {
			$sql = "SELECT
           			depth,disp_name,disp_no,parent_no
           		FROM sellertool_received_category
           		WHERE
           			site_code = '".$this->site_code."' ";
		 
			
			$cnames = explode(" ", $cname);
			if(count($cnames) > 0){
				for($i=0;$i < count($cnames);$i++){
					if($i==0){
						$inner_sql .= " disp_name LIKE '%" . $cnames[$i] . "%' ";
					}else{
						$inner_sql .= " or disp_name LIKE '%" . $cnames[$i] . "%' ";
					}
				}
				$sql .= "AND ( ".$inner_sql." )";
			}
			//$sql .= "AND disp_no LIKE '0012%' ";
	 
			$sql .= " ORDER BY  disp_no asc ,  disp_name ASC limit 200 ";
			//echo $sql;
			$this->db->query ( $sql );
			if ($this->db->total) {
				$result = $this->db->fetchall ( "object", MYSQL_ASSOC );
				$key = 0;
				foreach ( $result as $rt ) :
					$return [$key] = new categoryData ();
					$return [$key]->depth = $rt ['depth'];
					$return [$key]->disp_name = $rt ['disp_name']."(".$rt ['depth'].")";
					$return [$key]->category_path = getSellertoolReceivedCategoryPathByAdmin($rt ['disp_no'], $this->site_code);
					$return [$key]->disp_no = $rt ['disp_no'];
					$return [$key]->parent_no = $rt ['parent_no'];
					
					$key ++;
				endforeach
				;
			} else {
				$return = NULL;
			}
		}
		return $return;
	}
}
