<?php

namespace App\Http\Controllers;

use App\CV;
use App\Job;
use Illuminate\Http\Request;
use Elasticsearch\ClientBuilder;

class CrawlController extends Controller
{

    /**
     *
     */
    public function readDataFromDB()
    {
        $client = ClientBuilder::create()->build();

        dd(1);

        $temp_data = CV::all()->toArray();
        $temp_string = "";
        $data = [];
        foreach ($temp_data as $cv) {
            $cv["workExperience"] = json_decode($cv["workExperience"],true);
            foreach($cv["workExperience"] as $item) {
                $temp_string .= $item["title"]." ".$item["company"]." ,";
            }
            $cv["workExperience"] = $temp_string;
            $temp_string = "";


            $cv["education"] = json_decode($cv["education"],true);
            foreach($cv["education"] as $item) {
                $temp_string .= $item["degree"]." ".$item["university"]." ,";
            }
            $cv["education"] = $temp_string;
            $temp_string = "";


            $cv["skills"] = json_decode($cv["skills"],true);
            foreach($cv["skills"] as $item) {
                $year = $item["monthsOfExperience"]/12 < 1? $item["monthsOfExperience"]." months " : $item["monthsOfExperience"]/12 ." years ";
                $temp_string .= $item["skill"]." ".$year." ,";
            }
            $cv["skills"] = $temp_string;
            $temp_string = "";



            $cv["awards"] = json_decode($cv["awards"],true);
            foreach($cv["awards"] as $item) {
                $temp_string .= $item["title"]." ".$item["description"]." ,";
            }
            $cv["awards"] = $temp_string;
            $temp_string = "";

            $cv["certifications"] = json_decode($cv["certifications"],true);
            foreach($cv["certifications"] as $item) {
                $temp_string .= $item["title"]." ".$item["description"]." ,";
            }
            $cv["certifications"] = $temp_string;
            $temp_string = "";


            $cv["groups"] = json_decode($cv["groups"],true);
            foreach($cv["groups"] as $item) {
                $temp_string .= $item["title"]." ".$item["description"]." ,";
            }
            $cv["groups"] = $temp_string;
            $temp_string = "";

            $cv["cv"] = $cv["fullname"]." ".$cv["location"]." ".$cv["summary"]." ".$cv["workExperience"]." ".$cv["education"]." ".$cv["skills"];


            unset($cv["links"],$cv["militaryService"],$cv["patents"],$cv["publications"],$cv["created_at"],$cv["updated_at"]);
            $data[] = $cv;
        }




        //Clean index
        foreach ($data as $item) {
            $params = [
                'index' => 'cv',
                'type' => 'cv',
                'body' => $item,

            ];
            $client->index($params);
        }


//        $data = Job::where('id','>','986')->get()->toArray();
//
//        foreach ($data as $key=>$line) {
//
//            //Index document
//            $params = [
//                'index' => 'job',
//                'type' => 'job',
//                'body' => [
//                    'id' => $line["id"],
//                    'title' => $line['title'],
//                    'description' => $line['description']
//                ]
//            ];
//
//            $client->index($params);
//        }

        $params = [
            "size" => 1000,
            "index" => "cv",
            'type' => 'cv',
            "body" => [
                "query" => [
                    "match_all" => new \stdClass()
                ]
            ]
        ];

        $response = $client->search($params);
        dd($response);

    }

    /**
     *
     */
    public function createIndex()
    {
        $client = ClientBuilder::create()->build();

        $params = [
            'index' => 'recommendationsystem'
        ];

        $response = $client->indices()->create($params);
        dd($response);
    }

    /**
     *
     */
    public function deleteIndex()
    {
        $client = ClientBuilder::create()->build();

        $params = [
            'index' => 'recommendationsystem'
        ];

        $response = $client->indices()->delete($params);
        dd($response);
    }

    /**
     *
     */
    public function clearDataInIndex()
    {
        $client = ClientBuilder::create()->build();
        $params = [
            "size" => 9999,
            "index" => "cv",
            'type' => 'cv',
            "body" => [
                "query" => [
                    "match_all" => new \stdClass()
                ]
            ]
        ];

        $response = $client->search($params);
        dd($response);
        foreach ($response["hits"]["hits"] as $document) {
            $params_delete = [
                "index" => "cv",
                "type" => "cv",
                "id" => $document["_id"]
            ];

            $client->delete($params_delete);
        }
    }

    /**
     *
     */
    public function putSettingIndex()
    {
        $client = ClientBuilder::create()->build();


        $params = [
            'index' => 'cv',
            'body' => [
                "settings" => [
                    "analysis" => [
                        "analyzer" => [
                            "my_analyzer" => [
                                "type" => "standard",
                                'filter' => ['lowercase', 'stop', 'kstem'],
                                "stopwords" => "_english_"
                            ]
                        ]
                    ]
                ]
            ]
        ];

        dd($client->indices()->putSettings($params));
    }


    /**
     *
     */
    public function putMappingIndex()
    {
        $client = ClientBuilder::create()->build();


        $params = [
            'index' => 'cv',
//            "type" => "cv",
//            'body' => [
//                    "cv" => [
//                        '_source' => [
//                            'enabled' => true
//                        ],
//                        "properties" => [
//                            "id" => [
//                                "type" => "integer",
//                            ],
//                            "fullname" => [
//                                "type" => "text",
//                                'analyzer' => 'my_analyzer',
//                                'term_vector' => 'yes',
//                            ],
//                            "location" => [
//                                "type" => "text",
//                                'analyzer' => 'my_analyzer',
//                                'term_vector' => 'yes',
//                            ],
//                            "headline" =>[
//                                "type" => "text",
//                                'analyzer' => 'my_analyzer',
//                                'term_vector' => 'yes',
//                            ],
//                            "summary" => [
//                                "type" => "text",
//                                'analyzer' => 'my_analyzer',
//                                'term_vector' => 'yes',
//                            ],
//                            "email" => [
//                                "type" => "text",
//                                'analyzer' => 'my_analyzer',
//                                'term_vector' => 'yes',
//                            ],
//                            "phoneNumber" => [
//                                "type" => "text",
//                                'analyzer' => 'my_analyzer',
//                                'term_vector' => 'yes',
//                            ],
//                            "additionalInformation" => [
//                                "type" => "text",
//                                'analyzer' => 'my_analyzer',
//                                'term_vector' => 'yes',
//                            ],
//                            "workExperience" => [
//                                "type" => "text",
//                                'analyzer' => 'my_analyzer',
//                                'term_vector' => 'yes',
//                            ],
//                            "education" => [
//                                "type" => "text",
//                                'analyzer' => 'my_analyzer',
//                                'term_vector' => 'yes',
//                            ],
//                            "skills" => [
//                                "type" => "text",
//                                'analyzer' => 'my_analyzer',
//                                'term_vector' => 'yes',
//                            ],
//                            "awards" => [
//                                "type" => "text",
//                                'analyzer' => 'my_analyzer',
//                                'term_vector' => 'yes',
//                            ],
//                            "certifications" => [
//                                "type" => "text",
//                                'analyzer' => 'my_analyzer',
//                                'term_vector' => 'yes',
//                            ],
//                            "groups" => [
//                                "type" => "text",
//                                'analyzer' => 'my_analyzer',
//                                'term_vector' => 'yes',
//                            ],
//                            "cv" => [
//                                "type" => "text",
//                                'analyzer' => 'my_analyzer',
//                                'term_vector' => 'yes',
//                            ],
////                            "title" => [
////                                "type" => "text",
////                                'analyzer' => 'my_analyzer',
////                                'term_vector' => 'yes',
////                            ],
////                            "description" => [
////                                "type" => "text",
////                                'analyzer' => 'my_analyzer',
////                                'term_vector' => 'yes',
//                            ]
//                    ]
//                ]
//            ]
//
//            ]
        ];



        $response = $client->indices()->getMapping($params);
        dd($response);

    }


    // Format dữ liệu
    function formatHtml($html){
        return preg_replace('/\s+/i', ' ', $html);
    }

    /**
     *
     */
    public function crawlJob()
    {

        $array  = ["restaurant","bartender","Foundation","English","Teacher"];
        $url = "https://www.indeed.co.uk";

        foreach ($array as $item) {
            for($i=0;$i<101;$i+=10) {
                $html = $this->formatHtml(file_get_contents("https://www.indeed.co.uk/jobs?q=".$item."&l=&start=".$i));

                preg_match_all('/<h2\s+id=.*?\s+class="jobtitle">\s<a\shref="(?<link>[^"]+).*?/',$html,$matches);


                foreach ($matches["link"] as $link) {
                    $job_html = $this->formatHtml(file_get_contents($url.$link));
                    preg_match_all('/<b\sclass="jobtitle"><font.*?">(?<jobtitle>.*?)<\/font>.*?id="job_summary"\sclass="summary">(?<detail>.*?)<\/span>/',$job_html,$infos);
                    if (!empty($infos['jobtitle'][0]) && !empty($infos['detail'][0])) {
                        $obj['title'] = $infos['jobtitle'][0];
                        $obj['description'] = strip_tags($infos['detail'][0]);
                        Job::create($obj);
                    }

                }
            }
        }

    }

    /**
     *
     */
    public function crawlCV()
    {
        $array  = [
            "https://resumes.indeed.com/rpc/resume?accountKeys=428f560e9a11a39b%2Cc4f36fef8af7125d%2Cb2cadd8ba3dd15f9%2Cbb1456e0724d1066%2C018ec186f1e23275%2Ca2c8df29909c226b%2Cd158b6214a3d8325%2Cc95ed483b0bf3ad8%2C75123b0e4ba58aa4%2C36d1c08ab8de89c9%2C8827feca76736f25%2Cd6110d5d2cbad337%2C5988c38def58b6af%2Cf4547912da51b3c2%2C216be98bea61a3af%2C3f48a482e9e23905%2C0183863c4ee078a7%2C2637f69c94d0c4e1%2C2d10230f7714936d%2C92d69b21167e7f58%2Ccdaa2c28bf24d1e6%2Ccfa21a47575ed0e3%2C0f6bd6809b1a0068%2C86d5637a3712da2c%2Ca044bf4de44ff879%2C9ca2e8d508668554%2C85d264f1694b3fd8%2Ccff07144ac9442d6%2C48970eaf114c25b5%2Cfbb8ff13f4da61e7%2C8c794791f468bd5b%2C29ba65ceb51d299f%2C8c0a72b4c82cd776%2Cd98291628d913d4f%2C44f48efa1d07494e%2C64b4dc2ca1e1634f%2C67397b35e118173b%2Cdac526beaabad671%2C2852f270ef2cc95d%2Ce2337ed79aa2a16c%2Cc61a0b367cafd77c%2C7be11e7beb8f5ae3%2C6bc3c85d5b1ec014%2Ca7591dc093b11868%2C76d9afaa286ea0e4%2Ce33ba68a49220d76%2Cdbe7cbaf230501ce%2Cc2211994fa16f537%2C405df7c27b9ba9ff%2Cf8d23249eda369d3&ctx=rozPreview&tk=1cbc2bg3e36opagp",
            "https://resumes.indeed.com/rpc/resume?accountKeys=d43faac346c36dec%2C627a476b9b7af689%2C5bd8502594b7d206%2C48777f7ade5c29a1%2Cdfe8543d5bb79615%2C00eb8ef3cb7e06c1%2C56fa5a91d2f0abb1%2Ca6be777cea34b1a9%2C725ab8359419b9a6%2Cccf8df125f85dec9%2C483e7e79d9856b19%2Cfe138b8ee3606ba4%2Cba5e3593418fcd64%2C8167aa48c5e24fcb%2C9b3457caabcbf08d%2C93e42cf7fcf34abf%2Cd4a006d162e83a02%2Cf2a117da7b65cddb%2Cd96a9d147f66d3eb%2Cdc13da267181006a%2C253dafb869d5c293%2C955fad3782f13347%2C8ef45bf7694d691b%2Cc6b3c409fd2b53a8%2C973ca97689b7ffd0%2Ce250bbe7190410a0%2C3a657b70343d9bbe%2Ca3adea4f8c7e9956%2Cfdc44fbf5691f4c9%2Cd363012c4de1744f%2C08bd76986596fba5%2Cf4abc700be74e2ef%2Ccc15e6bfdf6b9c2d%2C6d060cb35e957a37%2C329926d83e8f1986%2Cc78ccc62f245b058%2C71730248252936dc%2C95a610befe1d3d95%2Ca57a0f49887e65c3%2Cf1360e97f55ca10d%2Ca5246c6e210f7f56%2C17bf6e322b2260a9%2C806306eb520c2da2%2C3440bc0eec620f53%2C44885479c00b04d6%2Cb4ca42029320cc30%2Cdf4ee481b02fc440%2C21b7206550c36258%2C67cf6683b818c22c%2C30670d834862c46f&ctx=rozPreview&tk=1cbc26np736opbqo",
            "https://resumes.indeed.com/rpc/resume?accountKeys=3fb69ab342a4857f%2C7561808fe50cd4c9%2C17249df48bf95037%2Cee675d0a830b0e44%2C81fb0c89a3826c88%2C6783c78571916cf7%2Cd04d10a123b127c3%2C735acbfbe35fa490%2Cb39c07490ce7bbcb%2C3ae177734f6d9f64%2Ceabbef11d052cd0e%2C2d82f008197dfd7d%2Ce9029276c15a4484%2C41784da7b2937aed%2Cf236f41852a92fac%2C422551a038db3164%2Cfc4b4bac5b37fbd4%2C9d2a9ba73a9a40d4%2Ccd0f91c42bddcc43%2Cf020b01fec079f32%2C0cbcedc621a7ac27%2C1e793df12fce14a5%2Cad6d56673762c236%2C40759fdbb3f9d773%2C5561f335c5904d51%2C9cbff22b1aa700bc%2C53dacd09ecb4eee1%2Ccb63bfd265aa80ff%2C5aae0701d958a0e4%2C0afe5ece0f041f47%2Cac015c8d5defbb57%2Ca2cdef2b7414fcf8%2C074060d794d3ead9%2Cda20415c6847d822%2C35ade61a211297b8%2Cde5547350d9199f6%2Cc4b04b9357f271d8%2C6498639006786419%2Ca601c967be4c7409%2C6fd7814399f98e8a%2C7473a88df2db15cc%2Cf5330e479d02fc18%2C2b0709a039fe6d34%2C4dd9db72e150684f%2C60ee6678eda569b2%2Cb45c760b26d07e37%2C391ae0254af6ad48%2Cfb3820a89243d354%2C0c2cae550d948730%2C1ca22b1734db89d5&ctx=rozPreview&tk=1cbc3538v36opec0",
            "https://resumes.indeed.com/rpc/resume?accountKeys=de5d1a26f03c7317%2Caf13bd1040c799a2%2C4dd55a2174efd626%2Cf3560e3bc2ac281c%2C5d31e26a97a9736f%2C8cf1ae8e37173f7b%2C7dc6290b0dcea2b7%2Cde8d1027eeb8ac9d%2Cb1a273efee2695e4%2Cf7fc807f2f30fd1e%2Cdac526beaabad671%2C182d3e14255dc32c%2C0f5a7a5709905c90%2C71b572e6830266d9%2C1fe0ebbc062797f3%2C1417d3f693fd447f%2C5f3405d4cc0f3548%2C84b0c1bc502f0230%2C261e552db65f9ff8%2C23194d86002a5fbb%2C635d32aeb17340d2%2C20ef8482e6392897%2C805cc989b640ed87%2C30570f4f44cdf15b%2C09ba265f689e9597%2Cb2a39b726dc86fd6%2C45799e7c239efa62%2C90a1546c166e769a%2Cd10c3c5c37a99943%2C62854edad7a5dab3%2C58278e13810ee300%2C479f4ff2d587d622%2C10e735b60d8ccc4d%2C429e089c8144afc5%2Ce18c273243486d73%2C83122f931f24eb93%2C9be3f0d7ddee0d18%2Cea37269f939de5f5%2C39bf9d487aeae563%2Cc96ebc303fc067a5%2C468930c8e3835553%2C151061115ea86605%2Ce2756eafeee9c32d%2C02356bcf590ccccc%2Cf95fbb2d48884b98%2Ce8050fbb7a0cab72%2C13f4083ae8708de0%2C91e606ba9b0fb000%2C14eeb25918c1214b%2C42b27bc0eaab4131&ctx=rozPreview&tk=1cbc35rjm36oodge",
            "https://resumes.indeed.com/rpc/resume?accountKeys=785461c046ef5a89%2Ccc5290757068b427%2C175cc78916bdcd22%2C217c6c05aca740af%2C77f95a4dc92e31a8%2C947fa3e4ed41c7d0%2C5592b67387c09f3b%2Cbab1f154e74f07fe%2C763f91fae536d60b%2C61df98a7b027b5bb%2Cbd566b03b39af1d4%2Cca978092dc8232a7%2Cd4f3a9effd77fe8e%2C81df77a4c062d303%2C8001a6333f3772c5%2Cbc8b4e1d06d9f136%2C36c5846303edb0e3%2C858c0b003100ff5c%2C0ae09de4e4908303%2C40b86457b6340560%2C436ba1aed61e8d4f%2C70e58645ae5e817c%2C8b3f297a2718da14%2Ce5d885d33d5da07b%2C53bccfbebe466f6d%2Cce105ffb660f8a4f%2Cbd3aa286572a5ac1%2C1acbcea360f4f629%2C3076d9087dcee9ce%2C03e6a46084e4290e%2C2829b9fdabeb0fb5%2C2415049d8ad2e525%2C378b807224b136fe%2C4c8b6b168aa52c8d%2C0d3a5f0e197c27cb%2C792869c0809448cf%2Ce9d62ef81657fa19%2Cce8f5fc9e9cddc8e%2C0fe716e96e7981f5%2C84bcb931a4372262%2Cb453b866197a7967%2C7d3b49e3a9650e7a%2Cb72a9cc1124979de%2C6d41b0f3b0306ebe%2C69ce6d289dfcb465%2Ccc25f99b778b341e%2C7df1463229175bd7%2Ca1778faf345cbea2%2C34f5bbc52f2cbe6f%2C85a603fa4ea2eeb2&ctx=rozPreview&tk=1cbc3824t3c93b7s",
            "https://resumes.indeed.com/rpc/resume?accountKeys=b0416674bc70f372%2C2b34e053540d969d%2C65c1bfd8dd99ab56%2Cb463fab62891a867%2C07f64e2881e79ac6%2Ca5bf22e474fadc9b%2C2bc2ec7030e20935%2C899f844e10f99dc7%2C292fb7917874cc75%2Caceef1cd43761d61%2C9f775627cc41911e%2C645c06638358541c%2C173aaceda12635e7%2C5b907e5701c2c047%2C14cefb0434755569%2C8a901835ebde0182%2C6f54d538e33fcc9e%2C17f8e5b5744d1d40%2C179cb04a33604d3e%2C5e97881efa526d32%2C0d56f4cca342c0d0%2C5713dd544b4d81a5%2C76629c37a83afe85%2Cd55a32c8c64aca19%2C7daef3fa7674a916%2Cbfc183589b4e8e2a%2Cd20c33866db33d52%2C8df056ace6ed5230%2C7e94cfcdedf908bd%2C528b3fd186af1895%2C2e3c41abc0d182eb%2C34dd3ff1a8748f1b%2C25ba7e5f3e556893%2Ca51964d958f847d0%2Cbb21022a7a8366e1%2C8f043dcfb65b6448%2Cb8f5c686c637b943%2Ca38c33951636cdc3%2Cd55e16ff617ae29b%2Ceaeab517b441831b%2Cb4940c8d418f6916%2C99d9b5a34a6e0bb6%2C361e1672031bf47e%2Ca3792ca03585795a%2C2e292c894042c02e%2C2c148868ef4b91f1%2Ce7669bd5c5bcae28%2Cef290d7d00d4b4eb%2C73eb622339857fa7%2Cf49384a249ad64ca&ctx=rozPreview&tk=1cbc38oqf36op8rg",
            "https://resumes.indeed.com/rpc/resume?accountKeys=4cb5b815d6d4ec50%2Cfeda137e3b8e8a53%2Cb67eb5919bb9bd34%2Ca1c795d05259c8ac%2C1651455fc187bada%2Cd20ba99b72335fe3%2C85906aaa5e5458b9%2C7410892bbb1d3a94%2C7ad02383c5a3bfbe%2C900a73208af518db%2Cb32417f7ffa032c6%2C769e6c73ef347c05%2Cd53468a24e110520%2C63ffe72431168ba2%2C4e2aee4f20d68539%2C12e1b884e221d988%2C3cad5464a309325c%2C8bb43d9910a72ea0%2Cc517b225205f0c54%2C229833903b8c7fb7%2C21c92c675f8fda3e%2C7c071028705d3974%2C5576fc3ed5c07d32%2C28a34ef465620a77%2C2347a84a2fbfafa0%2C45f38723ca7bebf4%2Cb6b18fd54614a401%2C89e8e78ffb11a25e%2Ca6fcfd3664c708de%2C456a77bba3a61c30%2Cd09d48ce5fd49bca%2C52d522833477e46e%2C4d6ea738f0e7d4e8%2C087619bdfb7f753b%2Cf2a49e4d7ac96bae%2Cbb8cfa77da93f065%2C0e5cb71c1aec79a8%2C2336a5aa5091368f%2C9a8120f8e9bf3cf8%2C43dff615b3b6edbd%2C9353fce6149305b5%2C652fb330a5786548%2C74620acdef901eeb%2Cca1c94e87f798ec3%2C73262c6f5d3afa1e%2Cd6b47f069c12e9e8%2C52dfe527c692172e%2Cd09a20e413ffe22d%2C2ea62900c43dc421%2Cdc5853d49eeb1b6c&ctx=rozPreview&tk=1cbc397on3c93f76",
            "https://resumes.indeed.com/rpc/resume?accountKeys=6d041199ba49a151%2C1e7404f38358f20b%2C608857723719d5bb%2C228f658d2e3bfe74%2C4872a8d24c7099c4%2C37628fea53e98585%2Cec0db1be8ae18a32%2Cda0c71f201e6568d%2C1988057f3dcf0470%2C59092c2db44db430%2Ca00d0f4a3b7fe921%2C6b25789c9b904a44%2Cafc0c69fbf7db7c0%2C3f975f9fe8afefbf%2C83ec976bb7ce355f%2C1ad4f274f0b84386%2Cdd2a5e399048c20d%2Cdcd141910b5264e4%2C506b22c2f2ab1c1a%2C2ff15434043cd451%2Cb0b1bcb88c7400e9%2Ce13f251287a097bd%2C52e33bbca3b086a5%2Cc8ede84209bc7048%2Cfb1d354549c5fd11%2C1a1229b603f55edf%2C49b23c37cac9175c%2Ce3fba06e803f71db%2C8d0f3535fb3cf460%2C68d9436bdd8ec6b1%2C57ed31767a958d28%2Ce5c30e09570585b2%2C40e9df27e1ade428%2C6c84cbae1f099b2a%2C7a349fbcd4ff5f42%2Cb1ebd384e1009b9f%2C1c874682a4b7346e%2Cc0a3097564fe77cf%2C2625eabd28d21f66%2C62e64b98c03a2e88%2C9afc9e96022b1ddb%2C4a7c826e38715c49%2C93fda0b518a237d3%2C0979e4b1ec9f25ab%2Cdfce50dd83a7937b%2C58ada5384d588d6a%2Cad7987ed892cb1fd%2Cff75198b8793ae9e%2Cdd7b9a657f45514c%2Ca27312fe6793dc26&ctx=rozPreview&tk=1cbc39tq136op9u2",
            "https://resumes.indeed.com/rpc/resume?accountKeys=88883945fe706562%2C7da7a32952f8491b%2Ccd8b165ce3b8b417%2Cb7c389789199d9ca%2C3dad499370bb36df%2C12f84486a1e9945f%2Cf3cb93621f6defc4%2C0850e7020aedd63a%2C882bd8ee4ef82467%2Cbfa4ed04beda2cca%2C1753e01c2e743bf7%2C3418e8062edc709b%2C801d53379111a622%2Cea2fe451d1292e1f%2Cbbffe67a61d88656%2C22e629c4fa1c48f2%2C5d52b24a7a97bdc8%2C59bda1ccc6adc65b%2Cdb5093cfdf0fe359%2C185ca7ed8af437e2%2C697a80aaf8064a6b%2Ce5334d083650387c%2C1a4d6f2468f2d872%2Cfaba7cbcfa4a6fb6%2C0b88e69615d42c56%2C2f12689cad7caa5d%2C668d2c98237e8eb6%2Cbaedacd2dc0215dc%2Ce1c29770a77f2a6a%2Cc96425ca6a0a7edf%2C99bdc0e21b10efa3%2C4f86ab675a3fe519%2C8f4bad8e46d12958%2C47ad6c544609df64%2C011c460ba18937d0%2Cf741f044ca50aeac%2C75f30a81341f052a%2C3bdc22d9e28d3326%2C420f3a96a2fd319e%2C865bd109005fc642%2C2c42c09886f9a446%2C2683767c5825cb91%2C24b55c10ce7f1061%2C9d89d46c60b93393%2C6f73f7ba7ffa90b1%2C453951f1b21c3d0d%2C91ba5643d67ce519%2C4c4311f4b2610cb2%2C670a5ba976e39696%2C5c710b8c47bb287f&ctx=rozPreview&tk=1cbc3atni3c93anc",
            "https://resumes.indeed.com/rpc/resume?accountKeys=2651188793558557%2C7400dc6b77ebd5fb%2C300a9da00c084b14%2C65d9e1a56738e097%2C7b09e08039dc798b%2Caab40deba6e40d4c%2C1fa076adb4c5b6ab%2Cf28f2e815c363fa5%2C30bcb80c9e5316c7%2Ce2dde09fe2d71166%2C295fad44b57f116f%2C970dfb6e828a4d1e%2Ca1aaa1cad16dda4f%2C572d361439ec2241%2Cf9e883055daa69d9%2C8a91a2a312eb4ed8%2Cd83c5e86762d4399%2C113a68b35d1482e8%2Ca3bb09e1d53252d1%2C95b6ed1afae61681%2Cfe173d2524f0f648%2C7fec5e43b7d695e2%2C99997ae9c6caaa3e%2C6d7d3a35e8f7a7c6%2Cf282508b502b58b1%2Cf84b2d799c16b5a0%2C0a9a85053047c767%2C204d0df308fb5200%2C6540d7518e6d7fab%2C4e6ecfa908763b52%2Cc658a2901a21d62a%2C29ea747117b5bec7%2C57f1de03e7933720%2C17744a75aca6cc22%2Cc97af2ce37f7aa6c%2Cda03a4b2d99a8295%2Cef7fb76cc25ff4c7%2Cfd692cc2dacfc92c%2Ced2e6058ae32761b%2Cac6cf187045b6b8e%2C5134a6065ef5dd9c%2Ce6c6021c6c1b09ee%2C3be553aa88ac9f66%2Cdbfd6a70f01a93ad%2C4e19c45768f55049%2C0c79ce5783bc8787%2C8019642db9e1136b%2C946f29dfadda3ea5%2Ccbfc4c2d6b0668f8%2Cd0860986101a0003&ctx=rozPreview&tk=1cbc5cses3c93b2q",
            "https://resumes.indeed.com/rpc/resume?accountKeys=ed7b2332e60e249d%2Cb3407e3b543581e6%2Cfea5f2913cbdbdd4%2C617519da411bd87b%2C01cf89bc558582bb%2C8c3dba96faf24877%2Cb110014fde5048ed%2C577716901d0af9ed%2Cfe891d841ea8590c%2C5175076d78c925fc%2Cd433fd3438ab6130%2Ca356ebc507a61da8%2C345582772adab0f3%2Ce4abf59d25a9c288%2C9e0bdc9698829727%2C89207ca164ac07ec%2C533f26f73a7e3791%2C52d70b796b19fffa%2Ca046170544b13fc3%2C15e9dfb1c29f0fdb%2C655e0ba4cba028c8%2Cb05e963bf8123274%2Cf063ec488c64229d%2Ca47319f5fba67865%2C09bad45d23145340%2Cc6b1d3f4e5e9c3f2%2C49ac8e483f67241c%2Ce9d56aeca4c7c44e%2Cb07d0512474bf62b%2Cf2dd275913420f50%2C4b89e492f4be5f01%2Ce940a8a92fdd5a80%2Cb0b51c6b947ab10d%2C3b6f52a53df47178%2C76d54aaf069af3e2%2Cbd1a6e576721859c%2C0dd5b3febd4b9b7c%2Cf03ed8ed679dcaa8%2Ca87981c98e3a7455%2Cdf6b452a369e67a8%2C8ddbc40ea23b108a%2C272ae0ebeaea1f5b%2Cb46fc0c5fe04ba80%2Cc9bd40c1d95a63f9%2C8ac7e304c1aed9f9%2Ce3920ea98a0fa3d9%2Cb026eb867e5f5776%2Cdc7383ca98928e9c%2Cd5a60766e7ba90d0%2Ca53b592d683f6f58&ctx=rozPreview&tk=1cbc5en973c93den",
            "https://resumes.indeed.com/rpc/resume?accountKeys=9f94660389f767b6%2Cc318cee76423b741%2Cdbaeb69de302b31e%2Cdd6e7a4f59371c9e%2Cfb329a42f7397e91%2Ce79d895f705ada33%2Cc7c8a35e11ab581c%2C9d740cb28d083ca1%2C98c4dff5949c9862%2C30c7644b1c356273%2C8961d6b2c3b0c45e%2C062b68d1f65243bf%2C160792b2591c882f%2Ced9f067981df464c%2Ca69a85a5b9302c2d%2Cf218d27b0803ceae%2C7c930b9c5ae44cd8%2C3ae265b87321835a%2Ce2df8039a69af8f2%2C2d82b4df94f10f0f%2C9b4f571dece9295d%2Cca42332cbb0fbb4a%2C396c5559f4f1edf0%2C2764d2fd769b7121%2C8cd62fcbfce47619%2C928f64bc69916fbc%2C1fa2625f84fef7f5%2Cfcbb9f28b9b5801d%2C8653b0ec7813d15b%2C58d9878e2531a10f%2Cd73428661f49dda3%2Cf050af3d3ebd1831%2C6066d74b3925db8b%2C8ee2f80b2a9010ab%2C5c5b9472ff9982c4%2C8cd0ebf1b8bec492%2C104bf8793d377124%2C24d1706255978316%2C12b944311718e746%2C69d3ef688a393d6e%2Cfa86133a1c5016d5%2C9bf769031fef847e%2C0b567cacadf5a24c%2C494fd5eca526aeb0%2C30c308373c09be12%2C0bb821c6b7f22ec3%2C17659ab7762206c6%2Cd37da5e43d5014f9%2C138e87f289e7ac4b%2C76faabe1323a7533&ctx=rozPreview&tk=1cbc5fm8k36ooc7c",
            "https://resumes.indeed.com/rpc/resume?accountKeys=7e9f9aef42b6a399%2Ce739acbd1b4aaa06%2Cd08d533ac87aa929%2C7620c8786c9ac6a5%2C6c1332f89ced654b%2C4c90f7ffd453b91c%2Cae8670741e7a8962%2C8bd93a51ef4912f3%2C8032f1c7cdc7fe39%2C8eef25267b3e3df7%2Cd0456b7891ae7b88%2C8da46cf8038e644f%2C0e27c1da191fefa0%2C16c0003fe9a088dc%2Cfe6616a86e61c35e%2C47bb55233ef80553%2C04affd956e62115b%2C03de6e1b19b87041%2Ce126323027023892%2Cd345c95c3fdb3286%2C12503456a6ca8fe0%2Ce2d04bf4af02962c%2C1b7b83636db74874%2Cf2a50e0e2320bb56%2C9d4712a0a1b2d973%2Caed31a008c625ff6%2C06f507a03ed946e7%2Caa23bae5cb7cb6e4%2Ccae180e23e8240be%2Cd8182c9dfaab1e19%2C9f074af5de1c910e%2C3e41ec02f59b2807%2Cff8bb2310cd927b5%2Cfc27578ed1e7d60f%2C00cf9cefee44a82e%2C1e5f1c4c25b345d5%2Ce6c6ce254f3a7c87%2C964b58a748c6225e%2C018b9f13f880360d%2C319fe5771c6d047e%2Ce6cc46071cb14f45%2C84bf527804bbd1ae%2Cd59cf8119a804bf2%2C4a930c3fed4320f2%2Ce4a5a05d65f69d75%2C4724b6653d4b7de6%2C7acf934649b746a6%2Ce907d3cebbf174b8%2C0fbfe30b6374103d%2Cfc2ed385bfdef228&ctx=rozPreview&tk=1cbc5h7nn36op8l9",
            "https://resumes.indeed.com/rpc/resume?accountKeys=3c73e0568ccba0d1%2C09c7f504a08496a8%2C0e220caa638d79b0%2C637bb5c705dd711f%2C52d6ae4248343644%2Ce829776ef8a83bf7%2C11a5cbf9c24dd125%2C2ba450b16fdb0f94%2C893690b99c3e8f08%2C4dcd70cdc2a612f1%2Cf4767e08359be07a%2C9050fd8cfaef299e%2C477697fefcfdb600%2C654d2335f2df7161%2C0e528d0f1594453b%2C1e9c38b02e892fde%2C4c49e98b3647b7dd%2C85f1d6224450ff09%2Cffd6643a7240f406%2C9a1af851916d9005%2Cb6708b898c5c1358%2Ceb7d6637abdec99e%2C16295f5db367bcdf%2Ccfcb000e20315c52%2C3c4fbf27237adf43%2Cd883a501a2b299c6%2Cf1fc14e01f630879%2Ce5ce3beb6d5f2652%2C7adc6869829e0f11%2Ceabda7bfa6abc471%2Ca6196e9b21b34a5d%2C54286806472a65d3%2Cbc78c3f43b3fa8c5%2Cee316a65d67c4ae0%2C579873a44870cc22%2Cbdce52c36906146b%2C886dc5617cc54eab%2Ce494a2a1b67c66ce%2C448bcbd2df4fd20f%2Cf255ad1594c31049%2C09e98a99a1fea648%2C4fcc82bbf102033a%2Ce06cd98a62e11037%2C1989662a67469a43%2Cfb1f4e875a3d3d3b%2C16fa5ee6777d35f7%2Cccdf6586a44fa11c%2C152660712c639857%2Caf6f4f532b2828a1%2C3b2a77f7866f734c&ctx=rozPreview&tk=1cbc5hqh436ooem9",
            "https://resumes.indeed.com/rpc/resume?accountKeys=ee73aa143f99d469%2C80df713d76cd586f%2Cafc38c89c465d50a%2Cdfdb2f53adc8477f%2C2a29b01f395810dc%2C2cced5d065dd315d%2C795e1cc464d8100a%2C4444d9607818a362%2Cc979858034283f62%2Ca9998eb188fbb128%2Cc5894d8e6b8f1f27%2Cbbab7de617339b9a%2C3db42ab654d75c31%2C7d113497f096aa69%2Cfb5aca362925fe88%2C9a7726005ab45bec%2C43c1fc9fffa30b25%2Cc147e5c3935a39bf%2Cbe1b7fe002dfa6f9%2Cae5502319ffff98f%2C79b1cd01a7a37c87%2C89dd951c39326969%2C7e3a41e3b93c8dc4%2C1ad6e763c1ce04b0%2C293076470f723540%2Ccf8938a331bccf67%2C573936ca732b773d%2Cbf17f4381cafc331%2Cbb02b98a65b030fd%2Cc34dc4f876143e2b%2Cd61cbe8b37e4af98%2C6bc1dc78e2b5a979%2C48504f8297f44ea8%2C5a80da08cd6e0174%2C9f48c8e022f066fc%2Ca2b53143f75af9fe%2C661e12e45a35d915%2C87626a06a197d65a%2C1b67acd7f49a6151%2C1dc4b585d7e80189%2C51849edb79bf7500%2Cbb5717cdf0a79562%2C9505441ab5c47fe1%2C1de8a6177365e6c4%2Cb94efbbea1193971%2C1d940ddd7c484a53%2C9225bf87c80ab5e2%2C25efb323f5178b13%2Cd96b7fdbd320c639%2Ce28b1c8bcaab8e0e&ctx=rozPreview&tk=1cbc5jhqs36opanb",
            "https://resumes.indeed.com/rpc/resume?accountKeys=bc27a6883e7d4416%2Cb648fb02efc06aa1%2Cd211a3c599125f77%2C3e6c2d95433a8a47%2Cd0032cc4763f35a9%2C94bc55b50afa5c27%2Cbaf044da5577b620%2C0d8e2489ec00ed6f%2C3c9da4540abea804%2Cfdb84bed7972fdeb%2C9e59cf8f60ef4ab5%2Ca3be767289d2b373%2C442799f7b17c232d%2C3a62d4c8b8c04cf2%2C0593de5b1a4814a3%2C889852077ecde22b%2Cf2dddf1d243d32e8%2C44067e2919a190c7%2Cc70d0e658b46f5af%2C28677f4544fbb156%2C6ebd7f7df1a33c97%2Cec48302b0ec5d3a5%2C5c91bb370233cbe0%2Ca66e4e04d3fe3024%2Cda0a9512b35ba1cf%2C0c9f43875bf9ab05%2Cdc0e38c98eb07213%2Cb4ed196e011d0ddf%2Cc78b534a6492b2cc%2Cff9ca0b09bdfc37c%2C754120db91d0f45a%2C3ac2777ceadfa677%2C742e6a247166b285%2C1cd3c8b3c7d9e6cd%2C1adbc8f48f400a91%2C4e9c2c7000589d4a%2C2af5f0815078a22b%2C3d3c900f77a336f4%2Ca99818f416ba6f15%2Cb3b2e5b18de2cdec%2C002e81284fbba678%2C9d0448b7e210e8b2%2C520e822668a30451%2C58687cd416433568%2C6a819f45d956242f%2C11519ff47958cbd4%2C1ec3bb3ffecc2beb%2Ce18f12020aa32425%2C9bf7488fb4a24209%2Cf8fe3b3b8570a14a&ctx=rozPreview&tk=1cbc5k4vl36opb4f",

        ];


        foreach ($array as $item) {
                $html = $this->formatHtml(file_get_contents($item));
                $data = json_decode($html,true);

                foreach ($data["resumeModels"] as $cv) {
                    $cv["summary"] = preg_replace('/\n/',' ',$cv["summary"]);
                    $cv["workExperience"] = json_encode($cv["workExperience"]);
                    $cv["education"] = json_encode($cv["education"]);
                    $cv["skills"] = json_encode($cv["skills"]);
                    $cv["links"] = json_encode($cv["links"]);
                    $cv["militaryService"] = json_encode($cv["militaryService"]);
                    $cv["awards"] = json_encode($cv["awards"]);
                    $cv["certifications"] = json_encode($cv["certifications"]);
                    $cv["groups"] = json_encode($cv["groups"]);
                    $cv["patents"] = json_encode($cv["patents"]);
                    $cv["publications"] = json_encode($cv["publications"]);

                    unset($cv["accountKey"],$cv["updatedDate"],$cv["firstName"]);
                    CV::create($cv);
                }



        }
    }






}
