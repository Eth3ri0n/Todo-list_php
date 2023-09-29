<?php

//! ERRORS
const ERROR_REQUIRED = "Please add your Todo";
const ERROR_TOO_SHORT = "Please enter at least 5 characters";
$error = "";

$filename = __DIR__ . '/data/todos.json';
$todos = [];
$todo = "";

if (file_exists($filename)) {
    $data = file_get_contents($filename);
    $todos = json_decode($data, true) ?? [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $todo = $_POST['todo'] ?? '';
    if (!$todo) {
        $error = ERROR_REQUIRED;
    } elseif (mb_strlen($todo) < 5) {
        $error = ERROR_TOO_SHORT;
    }

    if (!$error) {
        $todos = [...$todos, [
            'id' => time(),
            'name' => $todo,
            'done' => false,
        ]];
        file_put_contents($filename, json_encode($todos));
        header('Location: /');
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once 'includes/head.php' ?>
    <title>Todo lists in PHP</title>
</head>

<body>
    <div class="container">
        <?php require_once 'includes/header.php' ?>
        <div class="content">
            <div class="todo-container">
                <h1>My Todo</h1>
                <form action="/" method="POST" class="todo-form">
                    <input type="text" name="todo" value="<?= $todo ?>">
                    <button class="btn btn-primary">Add</button>
                </form>
                <?php if ($error) : ?>
                <p class=" text-danger"><?= $error ?></p>
                <?php endif; ?>
                <ul class="todo-list">
                    <?php foreach ($todos as $t) : ?>
                    <li class="todo-item <?= $t['done'] ? 'low-opacity' : '' ?> ">
                        <span class="todo-name"><?= $t['name'] ?></span>
                        <a href=" /edit-todo.php?id=<?= $t['id'] ?>">
                            <button class="btn btn-primary btn-small"><?= $t['done'] ? 'Cancel' : 'Validate' ?></button>
                        </a>
                        <a href="/remove-todo.php?id=<?= $t['id'] ?>">
                            <button class="btn btn-danger btn-small">Delete</button>
                        </a>
                    </li>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>
        <?php require_once 'includes/footer.php' ?>
    </div>
</body>

</html>