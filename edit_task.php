<?php
require_once __DIR__ . '/config/db.php';
include __DIR__ . '/includes/header.php';

// Get task ID from URL
$task_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle form submission (UPDATE)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $title = htmlspecialchars(trim($_POST['title']), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($_POST['description']), ENT_QUOTES, 'UTF-8');
    $priority = htmlspecialchars(trim($_POST['priority']), ENT_QUOTES, 'UTF-8');
    $status = htmlspecialchars(trim($_POST['status']), ENT_QUOTES, 'UTF-8');
    $due_date = !empty($_POST['due_date']) ? htmlspecialchars(trim($_POST['due_date']), ENT_QUOTES, 'UTF-8') : NULL;
    $task_id = intval($_POST['task_id']);
    
    // Validate required fields
    if (empty($title)) {
        header("Location: edit_task.php?id=$task_id&error=Title is required");
        exit();
    }
    
    // Validate priority and status
    $valid_priorities = ['low', 'medium', 'high'];
    $valid_statuses = ['pending', 'completed'];
    
    if (!in_array($priority, $valid_priorities)) {
        $priority = 'medium';
    }
    
    if (!in_array($status, $valid_statuses)) {
        $status = 'pending';
    }
    
    // Prepare statement to prevent SQL injection
    $stmt = $mysqli->prepare("UPDATE tasks SET title = ?, description = ?, priority = ?, status = ?, due_date = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $title, $description, $priority, $status, $due_date, $task_id);
    
    if ($stmt->execute()) {
        header("Location: index.php?success=Task updated successfully");
    } else {
        header("Location: edit_task.php?id=$task_id&error=Failed to update task");
    }
    
    $stmt->close();
    $mysqli->close();
    exit();
}

// Fetch task details
$stmt = $mysqli->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->bind_param("i", $task_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: index.php?error=Task not found");
    exit();
}

$task = $result->fetch_assoc();
$stmt->close();
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square"></i> Edit Task
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form action="edit_task.php" method="POST">
                        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Task Title *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="title" 
                                   name="title" 
                                   value="<?php echo htmlspecialchars($task['title']); ?>" 
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="4"><?php echo htmlspecialchars($task['description']); ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="priority" class="form-label">Priority *</label>
                                <select class="form-select" id="priority" name="priority" required>
                                    <option value="low" <?php echo $task['priority'] == 'low' ? 'selected' : ''; ?>>Low</option>
                                    <option value="medium" <?php echo $task['priority'] == 'medium' ? 'selected' : ''; ?>>Medium</option>
                                    <option value="high" <?php echo $task['priority'] == 'high' ? 'selected' : ''; ?>>High</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending" <?php echo $task['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="completed" <?php echo $task['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="due_date" 
                                   name="due_date"
                                   value="<?php echo htmlspecialchars($task['due_date']); ?>">
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to List
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Task
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-3 border-danger">
                <div class="card-body">
                    <h5 class="text-danger">
                        <i class="bi bi-exclamation-triangle"></i> Danger Zone
                    </h5>
                    <p class="text-muted mb-3">Once you delete a task, there is no going back.</p>
                    <a href="delete_task.php?id=<?php echo $task['id']; ?>" 
                       class="btn btn-danger"
                       onclick="return confirm('Are you sure you want to delete this task? This action cannot be undone.');">
                        <i class="bi bi-trash"></i> Delete Task
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$mysqli->close();
include __DIR__ . '/includes/footer.php';
?>
