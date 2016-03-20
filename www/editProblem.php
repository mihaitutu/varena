<?php

require_once '../lib/Util.php';

Util::requireLoggedIn();

$id = Request::get('id');
$preview = Request::isset('preview');
$save = Request::isset('save');

$user = Session::getUser();

if ($id) {
  $problem = Problem::get_by_id($id);
  if (!$problem) {
    FlashMessage::add(_('Problem not found.'));
    Util::redirect(Util::$wwwRoot);
  }

  if (!$problem->editableBy($user)) {
    FlashMessage::add(_('You cannot edit this problem.'));
    Util::redirect("problem.php?id={$id}");
  }
} else {
  $problem = Model::factory('Problem')->create();
  $problem->userId = $user->id;
}

if ($save || $preview) {
  $problem->name = Request::get('name');
  $problem->statement = Request::get('statement');
  $problem->numTests = Request::get('numTests');
  $problem->testGroups = Request::get('testGroups');
  $problem->hasWitness = Request::get('hasWitness');
  $problem->evalFile = Request::get('evalFile');
  $problem->timeLimit = Request::get('timeLimit');
  $problem->memoryLimit = Request::get('memoryLimit');

  $errors = $problem->validate();
  if ($errors) {
    SmartyWrap::assign('errors', $errors);
  }
  if ($save && !$errors) {
    $problem->save();
    FlashMessage::add(_('Problem saved.'), 'success');
    Util::redirect("problem.php?id={$problem->id}");
  } else if ($preview) { // preview
    SmartyWrap::assign('previewed', true);
  }
}

SmartyWrap::assign('problem', $problem);
SmartyWrap::display('editProblem.tpl');

?>
