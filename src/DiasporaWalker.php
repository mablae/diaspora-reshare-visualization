<?php
/**
 * Created by JetBrains PhpStorm.
 * User: malte
 * Date: 23.12.12
 * Time: 19:28
 * To change this template use File | Settings | File Templates.
 */
class DiasporaWalker
{
    const MODE_TOROOT = "toroot"; // Gehe zur Wurzel des Baums
    const MODE_TREE   = "tree";   // Untersuche den Knoten auf Blatt-Knoten


    // Holds downloaded data
    private $cache;

    // final nodes and links
    private $resultTree;

    // worker list
    private $todo;


    /**
     * Recursive Walker
     *
     * @param string $startUrl
     * @param string $mode
     */
    public function __construct(ResultTree $resultTree, $startUrl = null, $mode = null) {
           $this->resultTree = $resultTree;
           $this->cache = array();
           $this->todo = array();
           $this->pushTodo($mode, array('url' => $startUrl));




    }

    public function start() {
        $this->dispatch();
    }


    /**
     * Downloads a websites and caches it. Returns data from cache, if URL already has been loaded
     *
     * @param string $url
     * @return string
     */
    private function getUrl($url)
    {
        if (isset($this->cache[$url])) {
            return $this->cache[$url];
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
        curl_setopt($ch, CURLOPT_URL, $url);

        $content = curl_exec($ch);
        curl_close($ch);

        $this->cache[$url] = $content;
        return $content;
    }

    /**
     * Untersucht, ob ein Diaspora-JSON-Array-Objekt einen Reshare darstellt.
     *
     * @param array stdClass
     * @return bool
     */
    private function isReshare($json)
    {
        return ($json->post_type == "Reshare") ? true : false;
    }

    private function buildPostQuery($host, $guid)
    {
        return "https://$host/posts/$guid.json";
    }

    private function buildInteractionsQuery($host, $guid)
    {
        return "https://$host/posts/$guid/interactions.json";
    }


    private function dispatch()
    {
        // Gibt es noch Jobs?
        if (!empty($this->todo)) {
            $job = array_shift($this->todo);
            if ($job['job'] == 'toroot') {
                $this->crawlToRoot($job['data']);
            } else {
                $this->inspectTree($job['data']);
            }
        }
        if (!empty($this->todo)) {

            // Call dispatch() again until all jobs done...
            $this->dispatch();
        }


    }


    /**
     * @param $data string
     */
    private function crawlToRoot($data)
    {
        $page = json_decode(
            $this->getUrl($data['url'])
        );

        // Basisinformationen zum Beitrag
        $host_parts = parse_url($data['url']);
        $host = $host_parts['host'];
        $author = $page->author->diaspora_id;
        $guid = $page->guid;

        if ($this->isReshare($page)) {
            // Weitere Infos, wenn es ein Reshare ist.
            $originalGuid = $page->root->guid;
            $originalAuthor = $page->root->author->diaspora_id;

            // Dieser Beitrag ist also ein Reshare.
            // Dann Lege mal die Wurzel zum weiteruntersuchen auf den Stack.
            $this->pushTodo(MODE_TREE, array(
                'url' => $this->buildInteractionsQuery($host, $originalGuid),
                'guid' => $originalGuid,
                'parent' => '0',
                'avatar' => $page->root->author->avatar->small
            ));

            // Anweisungen fürs Ajax
            return json_encode(
                array(
                    'command' => 'reload',
                    'msg' =>  "Reshare: $author teilt Beitrag von $originalAuthor"
                )
            );

        } else {
            // Der Beitrag ist gar kein Reshare, wir können also gleich mit dem nächsten Schritt weitermachen
            $this->pushTodo('tree', array(
                'url' => $this->buildInteractionsQuery($host, $guid),
                'guid' => $guid,
                'parent' => '0',
                'avatar' => $page->author->avatar->small
            ));

            $this->dispatch();
        }
    }

    /**
     * Diese Funktion sucht Informationen zum aktuellen Beitrag heraus
     * und setzt alle Reshares dieses Beitrags auf die Jobliste.
     */
    private function inspectTree($data)
    {
        $page = json_decode($this->getUrl($data['url']));

        // Basisinformationen zum Beitrag
        $host_parts = parse_url($data['url']);
        $host = $host_parts['host'];

        // Interaktionsinformationen:
        $sumLikes = count($page->likes);
        $sumComments = count($page->comments);
        $sumReshares = count($page->reshares);

        // Alle Reshares in die Queue packen
        $info = "";
        foreach ($page->reshares as $reshare) {
            $this->pushTodo('tree', array(
                'url' => $this->buildInteractionsQuery($host, $reshare->guid),
                'guid' => $reshare->guid,
                'parent' => $data['guid'],
                'avatar' => $reshare->author->avatar->small
            ));

            $info .= "Reshare | ThisGuid: " . $data['guid'] . ' --> ReshareGuid: ' . $reshare->guid . '<br />';
        }

        // Schauen, ob ein Avatar angegeben ist (=volle url), sonst default benutzen:
        if (substr($data['avatar'], 0, 4) != "http") {
            $data['avatar'] = "img/noavatar.png";
        }

        // Baue den Link zum eigentlichen Beitrag zusammen
        $linkToPost = "https://$host/posts/" . $data['guid'];

        // Anweisungen fürs Ajax
        $node_data = array(
            'parent' => $data['parent'],
            'guid' => $data['guid'],
            'walkLevel' => $data['parent'],
            'linkToPost' => $linkToPost,
            'sumLikes' => $sumLikes,
            'sumReshares' => $sumReshares,
            'sumComments' => $sumComments,
            'avatar' => $data['avatar'],
            'data' => $data,
            'htmlLink' => '<a href="' . $linkToPost . '" target="_blank"><img src="' . $data['avatar'] . '"><span><img src="img/heart.png">' . $sumLikes . '<br /><img src="img/comment.png">' . $sumComments . '</span></a>'
        );

        // Save the results to our ResultTree class
        $this->resultTree->addNode($node_data);
        $this->resultTree->addLink($node_data);
    }


    /**
     * Saves jobs to the Queue, which is processed by dispatch()
     *
     * @return boolean
     */
    private function pushTodo($job, $data)
    {
        if (array_push($this->todo, array('job' => $job, 'data' => $data))) {
            return true;

        }
        return false;
    }


    /**
     * Wrapper method to recieve the results
     *
     * @return string
     */
    public function getResults() {
        return $this->resultTree->getJson();
    }

}
