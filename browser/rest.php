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
	$profId = $request->get('profId', -1);
    	$params = ['limit' => $limit];
	$query = 'MATCH (p:Prof{IDprof: '.$profId.'})-[r:teaches]-(c:Course) RETURN p,r,c LIMIT {limit}';
    	$result = $neo4j->run($query, $params);
    	$identityMap = [];
    
	$nodeProfRead = false;
 	
    foreach ($result->records() as $record)
	{
		if (($record->get('p')->value('IDprof') ==  $profId) && (!$nodeProfRead))
        	{		
			$node = [
				'id' => $record->get('p')->value('IDprof'),
				'name' => $record->get('p')->value('lastname'),
				'firstname' => $record->get('p')->value('firstname'),
				'classes' => $record->get('p')->labels()[0],
				'selected' => true
			];
		
			if ($record->get('p')->value('IDprof') == $profId)
				$nodeProfRead = true;
		
			$nodes[] = [
				'data' => $node,
				'selected' => true
			];		
			 
			$node = array();
		}
		
        $identityMap[$record->get('p')->identity()] = count($node)-1;
        $node = [
		'id' => $record->get('c')->value('IDcourse'),
            	'name' => $record->get('c')->value('name'),
            	'classes' => $record->get('c')->labels()[0],
		'selected' => false
        ];
		
	$nodes[] = [
		'data' => $node,
		'selected' => false
	];		
			 
	$node = array();
	
        $identityMap[$record->get('c')->identity()] = count($node)-1;
        $edge = [
		'source' => $record->get('p')->value('IDprof'),
		'target' => $record->get('c')->value('IDcourse'),
		'selected' => false
        ];
		
	$edges[] = [
	        'data' => $edge,
		'selected' => false
	];
	$edge = array();
		
	}
			 
    $elements = [
        'nodes' => $nodes,
        'edges' => $edges
    ];
    $response = new JsonResponse();
    $response->setData($elements);
    return $response;
});


//match course with prof
$app->get('/matchcourse', function (Request $request) use ($neo4j) {
    $limit = $request->get('limit', 50);
	$courseId = $request->get('courseId', -1);
    $params = ['limit' => $limit];
    $query = 'MATCH (c:Course {IDcourse: '.$courseId.'} )<-[r:teaches]-(p:Prof) RETURN c,r,p LIMIT {limit}';
    $result = $neo4j->run($query, $params);
    $identityMap = [];

	$nodeCourseRead = false;
	
    foreach ($result->records() as $record)
	{
		if (($record->get('c')->value('IDcourse') == $courseId) && (!$nodeCourseRead))
        {			
			$node = [
				'id' => $record->get('c')->value('IDcourse'),
				'name' => $record->get('c')->value('name'),
				'classes' => $record->get('c')->labels()[0],
				'selected' => true
			];
			
			if ($record->get('c')->value('IDcourse') == $courseId)
				$nodeCourseRead = true;
		
			$nodes[] = [
				'data' => $node,
				'selected' => true
			];		
			 
			$node = array();
		}
		
			$identityMap[$record->get('c')->identity()] = count($nodes)-1;
			$node =[
				'id' => $record->get('p')->value('IDprof'),	
				'name' => $record->get('p')->value('lastname'),
				'firstname' => $record->get('p')->value('firstname'),
				'classes' => $record->get('p')->labels()[0],
				'selected' => false
			];
		
			$nodes[] = [
				'data' => $node,
				'selected' => false
	         ];		
			 
			$node = array();
		
			$identityMap[$record->get('p')->identity()] = count($nodes)-1;
			$edge = [
				'source' => $record->get('c')->value('IDcourse'),
				'target' => $record->get('p')->value('IDprof'),
				'selected' => false
            ];
			
			$edges[] = [
				'data' => $edge,
				'selected' => false
	         ];
			 
			$edge = array();		
    }
	
    $elements = [
        'nodes' => $nodes,
        'edges' => $edges
    ];
	
    $response = new JsonResponse();
    $response->setData($elements);
    return $response;
});


//search prof
$app->get('/search', function (Request $request) use ($neo4j) {
	
    $searchTerm = $request->get('q');
	
	$term = '(?i).*'.$searchTerm.'.*';
    	$queryp = 'MATCH (p:Prof) WHERE p.lastname =~ {term} RETURN p';
	$queryc = 'MATCH (c:Course) WHERE c.name =~ {term} RETURN c';
	$params = ['term' => $term];
    	$result = $neo4j->run($queryp, $params);
	
	$nodes = [];
    foreach ($result->records() as $record){
		$nodes[] = [ 'name' => $record->get('p')->value('lastname'),
		             'IDprof' => $record->get('p')->value('IDprof'), 
             		     'NodeTypeFormatted' => 'Angestellter' ];
    } 
	$result = $neo4j->run($queryc, $params);
	foreach ($result->records() as $record){
		$nodes[] = [ 'name' => $record->get('c')->value('name'),
		             'IDcourse' => $record->get('c')->value('IDcourse'),
             		     'NodeTypeFormatted' => 'Kurs' ];
    } 
    $response = new JsonResponse();
    $response->setData($nodes);
    return $response;
});

$app->run();
