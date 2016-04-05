<?php
/**
 * Created by PhpStorm.
 * User: xiang.chen
 * Date: 2015/11/26
 * Time: 14:55
 */
use Sphinx\SphinxClient as Sphinx;
class Search {

    private $sphinx;

    private $config_file = __DIR__."/Config.php";

    private $config     = [];

    private static $instance = null;

    /**
     * 构造函数
     */
    private function __construct() {
        require_once __DIR__."/SphinxClient.php";
        $this->config       = include($this->config_file);
        $this->sphinx       = new Sphinx();
        $this->sphinx->setServer($this->config['host'], $this->config['port']);
        $this->sphinx->setMatchMode(Sphinx::SPH_MATCH_EXTENDED2);
        $this->sphinx->setFieldWeights($this->config['weights']);
        $this->sphinx->setGroupBy('cas_number', Sphinx::SPH_GROUPBY_ATTR, '@relevance DESC , @count DESC, @id ASC');
        $this->sphinx->setSortMode(Sphinx::SPH_SORT_EXTENDED, '@weight DESC , @id ASC');
        $this->sphinx->setRankingMode(Sphinx::SPH_RANK_SPH04);
        $this->sphinx->setMaxQueryTime($this->config['timeout']);
    }

    /**
     * 静态
     */
    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new Search();
        }
        return self::$instance;
    }


    /**
     * moldata搜索
     * @param string $searchKeyword 搜索关键词
     * @param int $page 搜索页数
     * @param int $perPage 每页条数
     * @return array|bool
     * @author xiang.chen
     */
    public function searchMol($searchKeyword,$page = 1,$perPage = 10)
    {
        $filters = array(
            array("attr"=>"goods_id","type"=>'value','value'=>array(0),'exclude'=>false),
            array("attr"=>"mol_id",  "type"=>'value', 'value'=>array(0),'exclude'=>true)
        );
        $results   = $this->_getSphinxSearch($searchKeyword, $page, $perPage, "casdata" , $filters);
        if(!$results || empty($results['data'])) return $results;
        $tmp    = $results['data'];
        $results['data'] = [];
        foreach($tmp as $val) {
            !empty($val['attrs']['mol_id']) && $results['data'][] = $val['attrs']['mol_id'];
        }
        return $results;
    }

    /**
     * sphinx搜索代码
     * @param string $searchKeyword 搜索关键词
     * @param int   $page 搜索页数
     * @param int   $perPage 每页条数
     * @param array $filters 过滤条件，默认为空
     * @return array|bool
     * @author xiang.chen
     */
    private function _getSphinxSearch($searchKeyword, $page, $perPage, $indexes, $filters = array())
    {
        $keywords   = $this->prepareFulltextsearchKeywords($searchKeyword);
        $offset     = ($page - 1) * $perPage;
        $limit      = intval($perPage);

        $this->sphinx->setLimits($offset, $limit, $this->config['maxLimit']);

        if (!empty($filters)) {
            foreach ($filters as $filter) {
                if ($filter['type'] == 'value') {
                    $this->sphinx->setFilter($filter['attr'],$filter['value'],$filter['exclude']);
                } else if ($filter['type'] = 'range') {
                    $this->sphinx->setFilterRange($filter['attr'],$filter['min'],$filter['max'],$filter['exclude']);
                }
            }
        }
        $es_keywords = $this->sphinx->escapeString($keywords);
        $this->sphinx->addQuery(sprintf('^%s$ & "%s"', $es_keywords, $es_keywords), $indexes);
        $this->sphinx->addQuery($es_keywords, $indexes);
        $result = $this->sphinx->runQueries();
        $addonLimit = !isset($result['matches']) || count($result['matches']) == 0 ? $limit : (count($result['matches']) < $limit ? $limit - count($result['matches']) : 0);
        $keywords2  = $this->prepareFulltextsearchKeywords($searchKeyword, 1);
        if ($addonLimit && $keywords != $keywords2) {
            //search again
            $this->sphinx->setLimits(0, $addonLimit, $this->config['maxLimit']);
            $es_keywords2 = $this->sphinx->escapeString($keywords2);

            $this->sphinx->addQuery(sprintf('^%s$ & "%s"', $es_keywords2, $es_keywords2), $indexes);
            $this->sphinx->addQuery($es_keywords2, $indexes);

            $result2    = $this->sphinx->runQueries();
            $result2['matches'] = (!empty($result2[0]['matches']) ? $result2[0]['matches'] : []) + (!empty($result2[1]['matches']) ? $result2[1]['matches'] : []);
            $result2['words']   = !empty($result2[0]['words']) ? $result2[0]['words'] : [];
            $result2['total']   = (int)$result2[1]['total'];

            if ($result2 && $result2['matches']) {
                $result['matches']  = (!empty($result['matches']) ? $result['matches'] : []) + $result2['matches'];
                $result['total']    =  isset($result['total']) ? $result['total'] : 0;
                $result['total']    =  max($result['total'], count($result['matches']));
            }
        }

        $result['matches']  = (!empty($result[0]['matches']) ? $result[0]['matches'] : []) + (!empty($result[1]['matches']) ? $result[1]['matches'] : []);
        $result['words']    = !empty($result[0]['words']) ? $result[0]['words'] : [];
        $result['total']    = (int)$result[1]['total'];
        $result['time']     = (!empty($result[0]['time']) ? $result[0]['time'] : 0) + (!empty($result[1]['time']) ? $result[1]['time'] : 0);

        if ($result && $result['matches']) {
            //correction sort
            $this->correctionSearchCasSort($result['matches'], $searchKeyword);

            $results = array(
                'data'  => $result['matches'],
                'total' => $result['total'],
                'time'  => $result['time'],
                'start' => $offset,
                'limit' => $limit
            );
        } else {
            $results = false;
        }
        return $results;
    }

    /**
     * 处理搜索字符
     */
    private function prepareFulltextsearchKeywords($keyWords, $level = 0)
    {
        if ($level > -1) {
            /*
            $keysFile = ROOT_PATH . '/includes/search_replace_keywords.php';
            if( !file_exists($keysFile) || (!$keys = include($keysFile)) || !is_array($keys)) {
                return $keyWords;
            }*/

            //replace some similar words
            #$keyWords = str_replace(array_keys($keys[1]), array_values($keys[1]), $keyWords);

            //strip some unuseful words
            //$keyWords = str_replace($keys[2], '', $keyWords);

            //only leave chinese & english, if not formula.
            /*
            if(!preg_match('/^[a-z\d\s\(\)\.\-\+]+$/i', $keyWords)){
                $keyWords = preg_replace("/[^\x{4e00}-\x{9fa5}A-Za-z]+/u", ' ', $keyWords);
            }*/

            //$keyWords = str_replace(array('-','*'), ' ', $keyWords);
            $keyWords = preg_replace('/[-*\s\(\)]+/', ' ', $keyWords);
        }

        if ($level > 0) {
            $keys     = $this->config['filter_words'];
            $keyWords = str_replace($keys[2], '', $keyWords);

            $patternNotNormalChars = '/[^\x{4e00}-\x{9fa5}A-Za-z]+/u';
            if (preg_match($patternNotNormalChars, $keyWords)) {
                $keyWords = preg_replace($patternNotNormalChars, ' ', $keyWords);
            }/*else if(mb_strlen($keyWords,'UTF-8')>2){
                $keyWords = mb_substr($keyWords, 0, 2, 'UTF-8') . '*';
            }else{
                $keyWords = preg_replace('/([\x{4e00}-\x{9fa5}]{1})/u', " $1 ", $keyWords);
            }*/
        }
        return $keyWords;
    }

    /**
     * 处理搜索结果
     */
    private function correctionSearchCasSort(&$matches, $keyWords)
    {
        // $patternNotNormalChars = '/[^\x{4e00}-\x{9fa5}A-Za-z]+/u';
        $keyWordsL1 = $this->prepareFulltextsearchKeywords($keyWords, 0);
        $keyWordsL2 = $this->prepareFulltextsearchKeywords($keyWords, 1);
        $topItem = array();
        foreach ($matches as $id => $m) {
            $names = array();
            $names[] = $m['attrs']['name_cn'];
            $names[] = $m['attrs']['mol_name'];
            $names[] = $m['attrs']['goods_name'];
            $names[] = $m['attrs']['goods_english_name'];
            $names = array_merge($names, explode('; ', $m['attrs']['synonyms']));
            $names2 = preg_replace('/[^\x{4e00}-\x{9fa5}A-Za-z]+/u', '', $names);

            if (in_array($keyWords, $names) ||
                in_array($keyWordsL1, $names) ||
                in_array($keyWordsL2, $names) ||
                in_array($keyWords, $names2) ||
                in_array($keyWordsL1, $names2) ||
                in_array($keyWordsL2, $names2)
            ) {
                $topItem[$id] = $m;
                break;
            }
        }

        if ($topItem) {
            $matches = $topItem + $matches;
        }
    }
}