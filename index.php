<?php
require_once 'config/db.php';
include 'includes/header.php';

// Fetch all tasks from database
$sql = "SELECT * FROM tasks ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="display-4">
                    <i class="bi bi-list-task text-primary"></i> My Tasks
                </h1>
                <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                    <i class="bi bi-plus-circle"></i> Add New Task
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <?php
        $total_tasks = $result->num_rows;
        $completed_tasks = $conn->query("SELECT COUNT(*) as count FROM tasks WHERE status = 'completed'")->fetch_assoc()['count'];
        $pending_tasks = $conn->query("SELECT COUNT(*) as count FROM tasks WHERE status = 'pending'")->fetch_assoc()['count'];
        ?>
        
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-list"></i> Total Tasks</h5>
                    <h2 class="card-text"><?php echo $total_tasks; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-check-circle"></i> Completed</h5>
                    <h2 class="card-text"><?php echo $completed_tasks; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-hourglass-split"></i> Pending</h5>
                    <h2 class="card-text"><?php echo $pending_tasks; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Task List -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-list-ul"></i> Task List</h5>
                </div>
                <div class="card-body">
                    <?php if ($result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="30%">Title</th>
                                        <th width="35%">Description</th>
                                        <th width="10%">Priority</th>
                                        <th width="10%">Status</th>
                                        <th width="10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="taskList">
                                    <?php 
                                    $counter = 1;
                                    while($row = $result->fetch_assoc()): 
                                        $status_class = $row['status'] == 'completed' ? 'text-decoration-line-through text-muted' : '';
                                        $priority_badge = '';
                                        switch($row['priority']) {
                                            case 'high':
                                                $priority_badge = 'bg-danger';
                                                break;
                                            case 'medium':
                                                $priority_badge = 'bg-warning';
                                                break;
                                            case 'low':
                                                $priority_badge = 'bg-info';
                                                break;
                                        }
                                    ?>
                                    <tr data-task-id="<?php echo $row['id']; ?>" class="task-row">
                                        <td><?php echo $counter++; ?></td>
                                        <td class="<?php echo $status_class; ?>">
                                            <?php echo htmlspecialchars($row['title']); ?>
                                        </td>
                                        <td class="<?php echo $status_class; ?>">
                                            <?php echo htmlspecialchars($row['description']); ?>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $priority_badge; ?>">
                                                <?php echo ucfirst($row['priority']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input status-toggle" 
                                                       type="checkbox" 
                                                       data-task-id="<?php echo $row['id']; ?>"
                                                       <?php echo $row['status'] == 'completed' ? 'checked' : ''; ?>>
                                                <label class="form-check-label">
                                                    <?php echo ucfirst($row['status']); ?>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="edit_task.php?id=<?php echo $row['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="delete_task.php?id=<?php echo $row['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger delete-btn" 
                                               title="Delete"
                                               onclick="return confirm('Are you sure you want to delete this task?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                            <h4 class="text-muted mt-3">No tasks yet!</h4>
                            <p class="text-muted">Click "Add New Task" to create your first task.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTaskModalLabel">
                    <i class="bi bi-plus-circle"></i> Add New Task
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="add_task.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Task Title *</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="priority" class="form-label">Priority *</label>
                        <select class="form-select" id="priority" name="priority" required>
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$conn->close();
include 'includes/footer.php';
?>

