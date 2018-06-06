<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app = new \Slim\App;

$app->add(function ($request, $response, $next) {
  $response = $next($request, $response);
  return $response
          ->withHeader('Access-Control-Allow-Origin', '*')
          ->withHeader('Access-Control-Allow-Headers', '*')
          ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

$app->get('/tasks', function (Request $request, Response $response, array $args) {
  $sql = "SELECT * FROM tasks";
  try {
    $db = new db();
    $db = $db -> connect();
    $stmt = $db->query($sql);
    $tasks = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    $response->getBody()->write(json_encode($tasks));
  } catch(PDOEXception $e) {
    echo '{"error":'.$e.'}';
  }
});

$app->get('/tasks/{id}', function (Request $request, Response $response, array $args) {
  $id = $request->getAttribute('id');
  $sql = "SELECT * FROM tasks WHERE name = $id";
  try {
    $db = new db();
    $db = $db -> connect();
    $stmt = $db->query($sql);
    $tasks = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    $response->getBody()->write(json_encode($tasks));
  } catch(PDOEXception $e) {
    echo '{"error":'.$e.'}';
  }
});

$app->post('/tasks/add', function(Request $request, Response $response, array $args) {

  $name = $request->getParam('name');
  $description = $request->getParam('description');

  $sql = 'INSERT INTO tasks (name, description) VALUES(:name, :description)';
  $all = "SELECT * FROM tasks";

  try {
    $db = new db();
    $db = $db -> connect();
    $stmt = $db->prepare($sql);
    $stmt-> bindParam(':name', $name);
    $stmt-> bindParam(':description', $description);
    $stmt->execute();

    $all_stmt = $db->query($all);
    $all_stmt = $all_stmt->fetchAll(PDO::FETCH_OBJ);

    $response->getBody()->write(json_encode($all_stmt));
  } catch(PDOEXception $e) {
    echo '{"error":'.$e.'}';
  }
});

$app->put('/tasks/update/{id}', function(Request $request, Response $response, array $args) {

  $id = $request->getAttribute('id');
  $name = $request->getParam('name');
  $description = $request->getParam('description');

  $sql = 'UPDATE tasks SET name = :name, description = :description WHERE id = :id';
  $all = 'SELECT * FROM tasks';
  try {
    $db = new db();
    $db = $db -> connect();
    $stmt = $db->prepare($sql);
    $stmt-> bindParam(':name', $name);
    $stmt-> bindParam(':description', $description);
    $stmt-> bindParam(':id', $id);
    $stmt->execute();

    $all_stmt = $db->query($all);
    $all_stmt = $all_stmt->fetchAll(PDO::FETCH_OBJ);

    $response->getBody()->write(json_encode($all_stmt));
  } catch(PDOEXception $e) {
    echo '{"error":'.$e.'}';
  }
});

$app->delete('/tasks/delete/{id}', function (Request $request, Response $response, array $args) {
  
  $id = $args['id'];
  $sql = "DELETE FROM tasks WHERE id = :id";
  $all = "SELECT * FROM tasks";
  try {
    $db = new db();
    $db = $db -> connect();
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $all_stmt = $db->query($all);
    $all_stmt = $all_stmt->fetchAll(PDO::FETCH_OBJ);

    $response->getBody()->write(json_encode($all_stmt));
  } catch(PDOEXception $e) {
    echo '{"error":'.$e.'}';
  }
  return $response;
});

$app->delete('/tasks/delete/name/{name}', function (Request $request, Response $response, array $args) {
  
  $id = $request -> getAttribute('name');
  $sql = "DELETE FROM tasks WHERE name = :name";
  try {
    $db = new db();
    $db = $db -> connect();
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':name', $id);
    $stmt->execute();
    $response->getBody()->write('{"success":"true"}');
  } catch(PDOEXception $e) {
    echo '{"error":'.$e.'}';
  }
  return $response;
});