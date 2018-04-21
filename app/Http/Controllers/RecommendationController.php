<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Elasticsearch;
use Elasticsearch\ClientBuilder;
use MathPHP\LinearAlgebra\Vector;
use Phpml\Metric\Accuracy;

class RecommendationController extends Controller
{

    protected $client;

    public function __construct()
    {
        $client = ClientBuilder::create()->build();
        $this->client = $client;
    }

    /**
     * @throws \MathPHP\Exception\VectorException
     */
    public function matchData()
    {
        $this->matchCoreFunction("cv","job");

    }


    /**
     * @param $from
     * @param $to
     * @throws \MathPHP\Exception\VectorException
     */
    public function matchCoreFunction($from,$to)
    {
        $client = ClientBuilder::create()->build();

        $final_result = [];

        $json = '{
            "query" : {
                "match_all" : {}
                }
            }';

        //Number Job
        $params_from = [
            'index' => $from,
            'type' => $from,
        ];
        $count_from = $client->count($params_from);
        $count_from = $count_from["count"];

        //Number CV
        $params_to = [
            'index' => $to,
            'type' => $to,
        ];
        $count_to = $client->count($params_to);
        $count_to= $count_to["count"];

        //TermVector
        $from_search = '{
            "query" : {
                "match_all" : {}
                }
            }';
        $params = array_merge($params_from,[
            'size' => $count_from,
            'body' => $from_search,
        ]);
        $from_datas = $client->search($params);

        $i=0;

        //For each cv/job
        foreach($from_datas["hits"]["hits"] as $key_from=>$from_value) {


            //Build termVectors
            $field_term = $from == "cv" ? "cv" : ["title","description"];
            $params = array_merge($params_from,[
                "id" => $from_value["_id"],
                "fields" => $field_term,
                "term_statistics" => true,
            ]);
            $termvectors = $client->termvectors($params);

            //Build Vector CV => Build own vector
            $handleTermVector = $this->handleTermVector($termvectors, $from, $to, $from_value);
            $terms = $handleTermVector["terms"];
            $frequent = $handleTermVector["frequent"];
            $boost_score_ownVector = $handleTermVector["boost_score"];



            //Build vector term follow document
            $vectors = [];
            $count_terms = count($terms);
            $idf_docFreq = [];
            foreach($terms as $index=>$term) {
                $term = (string)$term;
                if ($to=="job") {
                    $json = [
                        'query' => [
                            'bool' => [
                                'should' => [
                                    ['term' => [ 'title' => $term ]],
                                    ['term' => [ 'description' => $term ]]
                                ]
                            ]
                        ]
                    ];
                } else {
                    $json = [
                        'query' => [
                            'bool' => [
                                'should' => [
                                    ['term' => [ 'fullname' => $term ]],
                                    ['term' => ["summary" => $term]],
                                    ['term' => [ 'location' => $term ]],
                                    ['term' => [ 'workExperience' => $term ]],
                                    ['term' => [ 'education' => $term ]],
                                    ['term' => [ 'skills' => $term ]],

                                ]
                            ]
                        ]
                    ];
                }
                $params_search = [
                    "index" => $to,
                    "type" => $to,
                    "size" => $count_to,
                    "body" => $json
                ];

                $response = $client->search($params_search);


                //Build vector
                foreach ($response["hits"]["hits"] as $document) {

                    //Change score
                    $score = $this->calculateBoostScoreTerm($term,$document,$to);
                    $document["_score"] *= $score;


                    //Add to list vectors
                    if(array_key_exists($document["_id"],$vectors)) {
                        $vectors[$document["_id"]][$index] = $document["_score"];
                    } else {
                        for ($j=0;$j<$count_terms; $j++) {
                            $j != $index ?$vectors[$document["_id"]][]=0 : $vectors[$document["_id"]][]=$document["_score"];
                        }
                    }
                }


                $idf_docFreq[] = $response["hits"]["total"];


            }


            //Calculate score cua CV
            $own_vc_vector = $this->calculateScore($frequent, $idf_docFreq, $count_to, $boost_score_ownVector);
            $result = [];
            $from_vector = new Vector($own_vc_vector);


            foreach ($vectors as $key=>$value) {
                $to_Vector = new Vector($value);
                $result[$key] = ($from_vector->dotProduct($to_Vector))/($from_vector->length()*$to_Vector->length());
            }


            //Final
            arsort($result);
            $final_result[$from_value["_id"]][] = $result;


            $params = [
                "index" => $to,
                "type" => $to,
                "id" => array_keys($result)[0]
            ];


            if ($i==112)
            {
                dd($client->get($params), $from_value, $result);
                dd($boost_score_ownVector,$own_vc_vector, $terms, $vectors["YGT_22IBJ81XLRnFXMES"]);
            }
            $i++;

        }


        file_put_contents("./result.json",json_encode($final_result));
    }


    /**
     * @param $from_id
     * @param $from
     * @param $to
     * @param $number_return
     * @return array
     * @throws \MathPHP\Exception\VectorException
     */
    public function searchMatchingSimilarity($from_id, $from, $to, $number_return)
    {

        $client = $this->client;
        $params_from = [
            'index' => $from,
            'type' => $from,
        ];

        $params_search_from = array_merge($params_from,[
            "body" => [
                "query" => [
                    "bool" => [
                        "must" => [
                            "term" => [
                                "id" => $from_id
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $from_value = $client->search($params_search_from);
        $from_value = $from_value["hits"]["hits"][0];

        $params_to = [
            'index' => $to,
            'type' => $to,
        ];
        $count_to = $client->count($params_to);
        $count_to= $count_to["count"];


        //
        //Build termVectors
        $field_term = $from == "cv" ? "cv" : ["title","description"];
        $params = array_merge($params_from,[
            "id" => $from_value["_id"],
            "fields" => $field_term,
            "term_statistics" => true,
        ]);
        $termvectors = $client->termvectors($params);

        //Build Vector CV => Build own vector
        $handleTermVector = $this->handleTermVector($termvectors, $from, $to, $from_value);
        $terms = $handleTermVector["terms"];
        $frequent = $handleTermVector["frequent"];
        $boost_score_ownVector = $handleTermVector["boost_score"];


        //Build vector term follow document
        $vectors = [];
        $count_terms = count($terms);
        $idf_docFreq = [];
        foreach($terms as $index=>$term) {
            $term = (string)$term;
            if ($to=="job") {
                $json = [
                    'query' => [
                        'bool' => [
                            'should' => [
                                ['term' => [ 'title' => $term ]],
                                ['term' => [ 'description' => $term ]]
                            ]
                        ]
                    ]
                ];
            } else {
                $json = [
                    'query' => [
                        'bool' => [
                            'should' => [
                                ['term' => [ 'fullname' => $term ]],
                                ['term' => [ "summary" => $term]],
//                                ['term' => [ 'location' => $term ]],
                                ['term' => [ 'workExperience' => $term ]],
//                                ['term' => [ 'education' => $term ]],
                                ['term' => [ 'skills' => $term ]],

                            ]
                        ]
                    ]
                ];
            }
            $params_search = [
                "index" => $to,
                "type" => $to,
                "size" => $count_to,
                "body" => $json
            ];

            $response = $client->search($params_search);


            //Build vector
            foreach ($response["hits"]["hits"] as $document) {

                //Change score
                $score = $this->calculateBoostScoreTerm($term,$document,$to);
                $document["_score"] *= $score;


                //Add to list vectors
                if(array_key_exists($document["_id"],$vectors)) {
                    $vectors[$document["_id"]][$index] = $document["_score"];
                } else {
                    for ($j=0;$j<$count_terms; $j++) {
                        $j != $index ?$vectors[$document["_id"]][]=0 : $vectors[$document["_id"]][]=$document["_score"];
                    }
                }
            }


            $idf_docFreq[] = $response["hits"]["total"];


        }


        //Calculate score cua CV
        $own_vc_vector = $this->calculateScore($frequent, $idf_docFreq, $count_to, $boost_score_ownVector);
        $result = [];
        $from_vector = new Vector($own_vc_vector);


        foreach ($vectors as $key=>$value) {
            $to_Vector = new Vector($value);
            $result[$key] = ($from_vector->dotProduct($to_Vector))/($from_vector->length()*$to_Vector->length());
        }


        //Final
        arsort($result);

//        if($to=="job")dd($result);
        $list_top_result =  array_slice($result,0, $number_return);
        $data_list_top = [];

        foreach ($list_top_result as $key=>$item) {
            $params_result = array_merge($params_to,[
                "id" => $key
            ]);
            $data_list_top[] = $client->get($params_result);

        }

        return $data_list_top;


//        $final_result[$from_value["_id"]][] = $result;


//        $params = [
//            "index" => $to,
//            "type" => $to,
//            "id" => array_keys($result)[1]
//        ];

//        dd($client->get($params), $from_value, $result);

//        file_put_contents("./result.json",json_encode($final_result));


    }


    /**
     * @param $keyword
     * @param $to
     * @return mixed
     */
    public function searchKeyWord($keyword, $to)
    {
        $params_to = [
            'index' => $to,
            'type' => $to,
        ];


        if ($to == "cv") {
            $params_search_to = array_merge($params_to,[
                "body" => [
                    "query" => [
                        "multi_match" => [
                            "query" => $keyword,
                            "fields" => ["fullname^2","headline^2",'skills','wordExperience']
                        ]

                    ]
                ]
            ]);

        } else {
            $params_search_to = array_merge($params_to,[
                "body" => [
                    "query" => [
                        "multi_match" => [
                            "query" => $keyword,
                            "fields" => ["title^3","description"]
                        ]

                    ]
                ]
            ]);
        }

        $result = $this->client->search($params_search_to);

        return $result["hits"];
    }


    /**
     * @param $termvectors
     * @param $from
     * @param $to
     * @param $fromVector
     * @return array
     */
    public function handleTermVector($termvectors, $from, $to, $fromVector)
    {
        $result = [];
        $score = [];
        if ($from == "cv") {
            foreach ($termvectors["term_vectors"][$from]["terms"] as $key=>$item) {
                $key = (string)$key;
                $terms[] = $key;
                $frequent[] = $item["term_freq"];

                $temp_score = 1;
                if (preg_match('/\b'.$key.'\b/',strtolower($fromVector["_source"]["fullname"])) !== 0) {
                    $temp_score = 3;
                }
                else if (preg_match('/\b'.$key.'\b/',strtolower($fromVector["_source"]["skills"])) !== 0 || preg_match('/\b'.$key.'\b/',strtolower($fromVector["_source"]["workExperience"])) !== 0) {
                    $temp_score = 2;
                }
                $score[] = $temp_score;
            }
        } else {
            foreach ($termvectors["term_vectors"]["description"]["terms"] as $key=>$item) {
                $key = (string)$key;
                $terms[] = $key;
                $frequent[] = $item["term_freq"];

                $score[] = 1;
            }
            foreach ($termvectors["term_vectors"]["title"]["terms"] as $key=>$item) {
                $key = (string)$key;
                if (array_search($key,$terms) !== false) {
                    $frequent[array_search($key,$terms)] += $item["term_freq"];
                    $score[array_search($key,$terms)] = 4;
                } else {
                    $terms[] = $key;
                    $frequent[] = $item["term_freq"];
                    $temp_score = 1;
                    if (preg_match('/\b'.$key.'\b/',strtolower($fromVector["_source"]["title"])) !== 0) {
                        $temp_score = 4;
                    }
                    $score[] = $temp_score;
                }

            }
        }

        $result["terms"] = $terms;
        $result["frequent"] = $frequent;
        $result["boost_score"] = $score;

        return $result;
    }



    /**
     * @param $frequent
     * @param $idf_vector
     * @param $count_docs
     * @param $boost_score
     * @return array
     */
    public function calculateScore($frequent, $idf_vector, $count_docs, $boost_score)
    {
        $vector_return = [];
        foreach ($frequent as $key=>$freq) {
            $tf = (2.2*$freq)/(1.2+$freq);
            $idf = log(1+(($count_docs-$idf_vector[$key]+0.5)/($idf_vector[$key]+0.5)));


            $score = $tf*$idf*$boost_score[$key];
            $vector_return[] = $score;
        }
        return $vector_return;
    }




    /**
     * @param $term
     * @param $document
     * @param $to
     * @return int
     */
    public function calculateBoostScoreTerm($term, $document, $to)
    {
        $score = 1;
        if ($to == "cv") {
            if (preg_match('/\b'.$term.'\b/',strtolower($document["_source"]["fullname"])) !== 0) {
                $score = 3;
            } else if (preg_match('/\b'.$term.'\b/',strtolower($document["_source"]["skills"])) !== 0 || preg_match('/\b'.$term.'\b/',strtolower($document["_source"]["workExperience"])) !== 0) {
                $score = 2;
            }
        } else {
            if (preg_match('/\b'.$term.'\b/',strtolower($document["_source"]["title"])) !== 0) {
                $score = 4;
            }
        }

        return $score;
    }



}