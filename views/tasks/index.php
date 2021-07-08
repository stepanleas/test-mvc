<?php

$thColumnType = 'ASC';

if (isset($orderType)) {
    switch ($orderType) {
        case 'DESC':
            $thColumnType = 'ASC';
                break;
        case 'ASC':
            $thColumnType = 'DESC';
                break;
    }
}
?>

<?php /* if (isset($orderType) && $orderType === 'ASC') { echo '-up'; } else { echo '-down'; } */ ?>

<div class="container">
    <div id="table-upper-header" class="d-flex justify-content-end align-items-center">
        <a href="/create" class="btn btn-primary mt-2 mb-2">Add</a>
    </div>

    <table class="table table-striped table-bordered tasks-table">
        <thead class="thead-dark">
        <tr>
            <th
                onclick="sortTasks(this.dataset.column)"
                data-column="user_name"
                data-order-type="<?= $thColumnType ?>"
                id="sort-task-name"
                scope="col">
                    <div class="d-flex justify-content-between">
                        Name
                        <i class="fas fa-sort"></i>
                    </div>
            </th>
            <th
                data-column="user_email"
                onclick="sortTasks(this.dataset.column)"
                data-order-type="<?= $thColumnType ?>"
                scope="col">
                    <div class="d-flex justify-content-between">
                        Email
                        <i class="fas fa-sort"></i>
                    </div>
            </th>
            <th
                data-column="description"
                onclick="sortTasks(this.dataset.column)"
                data-order-type="<?= $thColumnType ?>"
                scope="col">
                <div class="d-flex justify-content-between">
                    Task
                    <i class="fas fa-sort"></i>
                </div>
            </th>
            <th
                data-column="status"
                onclick="sortTasks(this.dataset.column)"
                data-order-type="<?= $thColumnType ?>"
                scope="col">
                    <div class="d-flex justify-content-between">
                        Status
                        <i class="fas fa-sort"></i>
                    </div>
            </th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php
            if (isset($tasks)) {
                foreach ($tasks as $task) { ?>
                    <tr>
                        <td><?= $task['user_name'] ?></td>
                        <td><?= $task['user_email'] ?></td>
                        <td><?= $task['description'] ?></td>
                        <td>
                            <?php
                                // If the task has a completed mark
                                if ($task['completed'] === 1) { ?>
                                    <i title="Edited" class="fas fa-check icon-completed-task"></i>
                        <?php   }
                                // If the task was edited by the admin
                                if ($task['admin_edited'] === 1) { ?>
                                    <i title="Edited by admin" class="fas fa-user"></i>
                        <?php   }
                            ?>
                        </td>
                        <td>
                            <?php
                            if (isset($user) && is_array($user) && $user['role'] === 'admin') { ?>
                                <a href="/edit?id=<?= $task['id'] ?>" class="btn btn-success">Edit</a>
                                <button data-id="<?= $task['id'] ?>" onclick="deleteTask(this.dataset.id)" href="/delete?id=<?= $task['id'] ?>" class="btn btn-danger">Delete</button>
                            <?php }
                            ?>
                        </td>
                    </tr>
        <?php   }
            }
        ?>
        </tbody>
    </table>
    <input type="hidden" id="tasksOnPage" data-current-page="<?= $pageNo ?>" data-count-tasks="<?= count($tasks) ?>"">
    <?php
    // If the number of tasks is higher than 3, then display the pagination links
    if (isset($countAllTasks) && $countAllTasks > 3) { ?>
        <nav class="d-flex justify-content-center" aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item <?php if ($pageNo <= 1) { echo 'disabled'; } ?>">
                    <a class="page-link" href="<?php if ($pageNo <= 1) { echo '#'; } else { echo '?page=' . ($pageNo - 1); } ?>&orderType=<?= $orderType ?>&column=<?= $column ?>">Previous</a>
                </li>
                <?php
                    for ($page = 1; $page <= $totalPages; $page++) { ?>
                        <li class="page-item <?php if ($page == $pageNo) { echo 'active'; } ?>">
                            <a class="page-link" href="?page=<?= $page ?>&orderType=<?= $orderType ?>&column=<?= $column ?>"><?= $page ?></a>
                        </li>
                    <?php }
                ?>
                <li class="page-item <?php if ($pageNo >= $totalPages) { echo 'disabled'; } ?>">
                    <a class="page-link" href="<?php if ($pageNo >= $totalPages) { echo '#'; } else { echo '?page=' . ($pageNo + 1); } ?>&orderType=<?= $orderType ?>&column=<?= $column ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php } ?>
</div>

<script>

    /**
     * Get the number of tasks on this page,
     * if it is 1, then there are no records on
     * this page, so we need to redirect the user
     * to one step back
     */
    const tasksOnPage = document.getElementById('tasksOnPage');
    // The amount of tasks displayed on this page
    let countTasks = tasksOnPage.dataset.countTasks;
    // The current page in the pagination
    let currentPage = tasksOnPage.dataset.currentPage;


    // Get the order type
    let orderType = document.getElementById('sort-task-name').dataset.orderType.trim();

    // Delete the task
    function deleteTask(id) {
        let formData = new FormData();
        formData.append('id', id);

        axios.post('/delete', formData)
        .then(res => {
            let resp = res.data;
            switch (resp.type) {
                case 'danger':
                    alert(resp.message);
                        break;
                case 'success':
                    let url = '/';
                    if (currentPage - 1 != 0) {
                        url = '/?page=' + (currentPage - 1) + '&orderType=' + orderType;
                    }
                    if (countTasks == 1) {
                        window.location.replace(url)
                    } else {
                        window.location.reload();
                    }
                        break;
            }
        })
    }

    // Sort the task by name
    function sortTasks(columnName) {
        window.location.replace('/?page=' + currentPage + '&orderType=' + orderType + '&column=' + columnName);
    }

</script>