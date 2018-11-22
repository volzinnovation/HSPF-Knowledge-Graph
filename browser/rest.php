<?php
use Silex\Application;
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\JsonResponse,
    Symfony\Component\Yaml\Yaml;
use GraphAware\Neo4j\Client\ClientBuilder;

require './vendor/autoload.php';

$app = new Application();

if (false !== getenv('GRAPHSTORY_URL')) {
    $cnx = getenv('GRAPHSTORY_URL');
} 

else 
{
	$config = Yaml::parse(file_get_contents(__DIR__.'/config/config_server.yml'));
    $cnx = $config['neo4j_url'];
}

$neo4j = ClientBuilder::create()
    ->addConnection('default', $cnx)
    ->build();
	
$app->get('/', function () {
	return file_get_contents(__DIR__.'/static/index.html');
});

//match prof with course
$app->get('/matchprof', function (Request $request) use ($neo4j) {
    $limit = $request->get('limit', 50);
    $params = ['limit' => $limit];
    $query = 'MATCH (p:Prof)-[r:teaches]-(c:Course) RETURN p,r,c LIMIT {limit}';
    $result = $neo4j->run($query, $params);
    $nodes = [];
    $edges = [];
    $identityMap = [];
    foreach ($result->records() as $record){
        $nodes[] = [
            'lastname' => $record->get('p')->value('lastname'),
			'firstname' => $record->get('p')->value('firstname'),
			'school' => $record->get('p')->value('school'),
            'label' => $record->get('p')->labels()[0]
        ];
        $identityMap[$record->get('p')->identity()] = count($nodes)-1;
        $nodes[] = [
            'name' => $record->get('c')->value('name'),
            'label' => $record->get('c')->labels()[0]
        ];
        $identityMap[$record->get('c')->identity()] = count($nodes)-1;
        $edges[] = [
            'source' => $identityMap[$record->get('r')->startNodeIdentity()],
            'target' => $identityMap[$record->get('r')->endNodeIdentity()]
        ];
    }
    $data = [
        'nodes' => $nodes,
        'links' => $edges
    ];
    $response = new JsonResponse();
    $response->setData($data);
    return $response;
});

//match course with prof
$app->get('/matchcourse', function (Request $request) use ($neo4j) {
    $limit = $request->get('limit', 50);
    $params = ['limit' => $limit];
    $query = 'MATCH (c:Course)<-[r:teaches]-(p:Prof) RETURN c,r,p LIMIT {limit}';
    $result = $neo4j->run($query, $params);
    $nodes = [];
    $edges = [];
    $identityMap = [];
    foreach ($result->records() as $record){
        $nodes[] = [
            'name' => $record->get('c')->value('name'),
            'label' => $record->get('c')->labels()[0]
        ];
        $identityMap[$record->get('c')->identity()] = count($nodes)-1;
        $nodes[] =[
            'lastname' => $record->get('p')->value('lastname'),
			'firstname' => $record->get('p')->value('firstname'),
            'label' => $record->get('p')->labels()[0]
        ];
        $identityMap[$record->get('p')->identity()] = count($nodes)-1;
        $edges[] = [
            'source' => $identityMap[$record->get('r')->startNodeIdentity()],
            'target' => $identityMap[$record->get('r')->endNodeIdentity()]
        ];
    }
    $data = [
        'nodes' => $nodes,
        'links' => $edges
    ];
    $response = new JsonResponse();
    $response->setData($data);
    return $response;
});

//search prof
$app->get('/search', function (Request $request) use ($neo4j) {
	
    $searchTerm = $request->get('q');
    
	//error_log("/searchprof entry. Searchteram: " . $searchTerm);
	
	$term = '(?i).*'.$searchTerm.'.*';
    $queryp = 'MATCH (p:Prof) WHERE p.lastname =~ {term} RETURN p';
	$queryc = 'MATCH (c:Course) WHERE c.name =~ {term} RETURN c';
	$params = ['term' => $term];
    $result = $neo4j->run($queryp, $params);
	
	// DEBUG
	//var_dump($result);
	
	$nodes = [];
    foreach ($result->records() as $record){
        //$prof[] = ['prof' => $record->get('p')->values()];
		//$prof[] = $record->get('p')->values();
		$nodes[] = [ 'name' => $record->get('p')->value('lastname'),
             		'NodeTypeFormatted' => 'Angestellter' ];
    } 
	$result = $neo4j->run($queryc, $params);
	foreach ($result->records() as $record){
        //$prof[] = ['prof' => $record->get('p')->values()];
		//$prof[] = $record->get('p')->values();
		$nodes[] = [ 'name' => $record->get('c')->value('name'),
             		'NodeTypeFormatted' => 'Kurs' ];
    } 
    $response = new JsonResponse();
    $response->setData($nodes);
    return $response;
});

$app->run();