<div class="container">
    <?php

    // Verify if the user is creating or updating the task
    $isEdit = $task ?? false;

    $id = 'createTask';
    $btn = 'Create';
    if ($isEdit) {
        $id = 'updateTask';
        $btn = 'Update';
    }

    $checked = '';
    if (isset($task) && !is_bool($task) && $task['completed'] === 1) {
        $checked = 'checked';
    }

    ?>
    <h1><?php if ($isEdit) { ?> Edit Task <?php } else { ?> Create Task <?php } ?></h1>
    <div class="row">
        <div class="col">
            <div class="form-group">
                <label>Username</label>
                <input value="<?= $task['user_name'] ?? '' ?>" id="userName" name="name" type="text" class="form-control">
            </div>
            <div class="form-group">
                <label>User email</label>
                <input value="<?= $task['user_email'] ?? '' ?>" id="userEmail" name="email" type="email" class="form-control">
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label>Description</label>
                <textarea id="taskDescription" class="form-control task-area"><?= $task['description'] ?? '' ?></textarea>
            </div>
        </div>
    </div>
    <div class="form-check">
        <input class="form-check-input" <?= $checked ?> type="checkbox" value="1" id="taskCompleted">
        <label class="form-check-label" for="taskCompleted">
            Completed
        </label>
    </div>
    <button id="<?= $id ?>" type="submit" class="btn btn-primary mt-3"><?= $btn ?></button>
</div>

<script>
    let btn = document.getElementById('createTask');
    if (btn === null) {
        btn = document.getElementById('updateTask');
    }

    btn.onclick = () => {
        let formData = new FormData();
        formData.append('user_name', document.getElementById('userName').value);
        formData.append('user_email', document.getElementById('userEmail').value);
        formData.append('description', document.getElementById('taskDescription').value);


        /**
         * Verify the id to identify whether we are
         * creating or updating the task
         */
        let url = '/store';
        if (btn.id === 'updateTask') {
            url = '/update';
            // Get the id from the url
            let urlStr = location.href;
            let id = urlStr.substring(urlStr.indexOf("?")+4);
            formData.append('id', id);
        }

        // Verify if the checkbox was clicked
        let completedStatus = document.getElementById('taskCompleted');
        if (completedStatus.checked) {
            formData.append('completed', completedStatus.value);
        }

        axios.post(url, formData)
            .then((resp) => {
                const data = resp.data;
                // Verify if the validation was successfully or not
                switch (data.type) {
                    case 'danger':
                        alert(data.message);
                        break;
                    default:
                        window.location.replace('/');
                            break;
                }

            })
            .catch((e) => {

            })
    }
</script>