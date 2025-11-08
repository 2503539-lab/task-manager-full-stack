<?php
require_once __DIR__ . '/config/db.php';
include __DIR__ . '/includes/header.php';

// Initialize search parameters
$search_title = isset($_GET['title']) ? htmlspecialchars(trim($_GET['title']), ENT_QUOTES, 'UTF-8') : '';
$search_status = isset($_GET['status']) ? htmlspecialchars(trim($_GET['status']), ENT_QUOTES, 'UTF-8') : '';
$search_priority = isset($_GET['priority']) ? htmlspecialchars(trim($_GET['priority']), ENT_QUOTES, 'UTF-8') : '';
$search_date_from = isset($_GET['date_from']) ? htmlspecialchars(trim($_GET['date_from']), ENT_QUOTES, 'UTF-8') : '';
$search_date_to = isset($_GET['date_to']) ? htmlspecialchars(trim($_GET['date_to']), ENT_QUOTES, 'UTF-8') : '';

// Build dynamic SQL query with security
$sql = "SELECT * FROM tasks WHERE 1=1";
$params = [];
$types = "";

if (!empty($search_title)) {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
    $search_param = "%$search_title%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

if (!empty($search_status) && in_array($search_status, ['pending', 'completed'])) {
    $sql .= " AND status = ?";
    $params[] = $search_status;
    $types .= "s";
}

if (!empty($search_priority) && in_array($search_priority, ['low', 'medium', 'high'])) {
    $sql .= " AND priority = ?";
    $params[] = $search_priority;
    $types .= "s";
}

if (!empty($search_date_from)) {
    $sql .= " AND due_date >= ?";
    $params[] = $search_date_from;
    $types .= "s";
}

if (!empty($search_date_to)) {
    $sql .= " AND due_date <= ?";
    $params[] = $search_date_to;
    $types .= "s";
}

$sql .= " ORDER BY created_at DESC";

// Prepare and execute statement using $mysqli
$stmt = $mysqli->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="display-4 mb-4">
                <i class="bi bi-search text-primary"></i> Search Tasks
            </h1>
        </div>
    </div>

    <!-- Search Form -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-funnel"></i> Search Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="search.php" id="searchForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">
                                    <i class="bi bi-card-text"></i> Search by Title/Description
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="title" 
                                       name="title" 
                                       placeholder="Enter keywords..."
                                       value="<?php echo $search_title; ?>"
                                       autocomplete="off">
                                <div id="autocomplete-results" class="autocomplete-suggestions"></div>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label for="status" class="form-label">
                                    <i class="bi bi-check-circle"></i> Status
                                </label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All</option>
                                    <option value="pending" <?php echo $search_status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="completed" <?php echo $search_status == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <label for="priority" class="form-label">
                                    <i class="bi bi-flag"></i> Priority
                                </label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="">All</option>
                                    <option value="low" <?php echo $search_priority == 'low' ? 'selected' : ''; ?>>Low</option>
                                    <option value="medium" <?php echo $search_priority == 'medium' ? 'selected' : ''; ?>>Medium</option>
                                    <option value="high" <?php echo $search_priority == 'high' ? 'selected' : ''; ?>>High</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_from" class="form-label">
                                    <i class="bi bi-calendar"></i> Due Date From
                                </label>
                                <input type="date" class="form-control" id="date_from" name="date_from"
                                       value="<?php echo $search_date_from; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_to" class="form-label">
                                    <i class="bi bi-calendar"></i> Due Date To
                                </label>
                                <input type="date" class="form-control" id="date_to" name="date_to"
                                       value="<?php echo $search_date_to; ?>">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to List
                            </a>
                            <div>
                                <a href="search.php" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-x-circle"></i> Clear
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Results -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-list-ul"></i> Search Results</h5>
                        <span class="badge bg-primary"><?php echo $result->num_rows; ?> task(s) found</span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($result->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $counter = 1;
                                    while($row = $result->fetch_assoc()): 
                                        $status_class = $row['status'] == 'completed' ? 'text-decoration-line-through text-muted' : '';
                                        $priority_badge = match($row['priority']) {
                                            'high' => 'bg-danger',
                                            'medium' => 'bg-warning',
                                            'low' => 'bg-info',
                                            default => 'bg-secondary'
                                        };
                                    ?>
                                    <tr>
                                        <td><?php echo $counter++; ?></td>
                                        <td class="<?php echo $status_class; ?>"><?php echo htmlspecialchars($row['title']); ?></td>
                                        <td class="<?php echo $status_class; ?>"><?php echo htmlspecialchars($row['description']); ?></td>
                                        <td><span class="badge <?php echo $priority_badge; ?>"><?php echo ucfirst($row['priority']); ?></span></td>
                                        <td><span class="badge <?php echo $row['status'] == 'completed' ? 'bg-success' : 'bg-warning'; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                        <td><?php echo $row['due_date'] ? date('M d, Y', strtotime($row['due_date'])) : 'N/A'; ?></td>
                                        <td>
                                            <a href="edit_task.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                            <a href="delete_task.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure?');"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-search" style="font-size: 4rem; color: #ccc;"></i>
                            <h4 class="text-muted mt-3">No tasks found</h4>
                            <p class="text-muted">Try adjusting your search criteria.</p>
                            <a href="index.php" class="btn btn-primary mt-3"><i class="bi bi-house"></i> Go to Home</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$stmt->close();
$mysqli->close();
include __DIR__ . '/includes/footer.php';
?>
