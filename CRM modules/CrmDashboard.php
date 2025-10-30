<?php
// Start session if not already started
session_start();

// Include database configuration
require_once('../config/database.php');

// Initialize database connection
try {
    $db = Database::getInstance()->getConnection();
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Get user information
$userId = $_SESSION['user_id'];

// Initialize CRM tables if they don't exist
function initializeCRMTables($db)
{
    // Create leads table if it doesn't exist
    $db->exec("CREATE TABLE IF NOT EXISTS leads (
        LeadID INT AUTO_INCREMENT PRIMARY KEY,
        FirstName VARCHAR(50) NOT NULL,
        LastName VARCHAR(50) NOT NULL,
        Email VARCHAR(100),
        Phone VARCHAR(20),
        CompanyName VARCHAR(255),
        JobTitle VARCHAR(100),
        PotentialValue DECIMAL(10,2) DEFAULT 0,
        Priority ENUM('Low', 'Normal', 'High', 'Urgent') DEFAULT 'Normal',
        Status ENUM('New', 'Contacted', 'Qualified', 'Proposal', 'Negotiation', 'Won', 'Lost') DEFAULT 'New',
        Source ENUM('Website', 'Referral', 'Social Media', 'Cold Call', 'Event', 'Other') DEFAULT 'Other',
        Notes TEXT,
        AssignedTo INT,
        CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
        UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_status (Status),
        INDEX idx_priority (Priority),
        INDEX idx_created (CreatedAt)
    )");

    // Create deals table if it doesn't exist
    $db->exec("CREATE TABLE IF NOT EXISTS deals (
        DealID INT AUTO_INCREMENT PRIMARY KEY,
        LeadID INT,
        DealName VARCHAR(255) NOT NULL,
        DealValue DECIMAL(10,2) NOT NULL,
        Stage ENUM('Prospecting', 'Qualification', 'Proposal', 'Negotiation', 'Closed Won', 'Closed Lost') DEFAULT 'Prospecting',
        Probability INT DEFAULT 0,
        CloseDate DATE,
        Notes TEXT,
        CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
        UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (LeadID) REFERENCES leads(LeadID) ON DELETE SET NULL,
        INDEX idx_stage (Stage),
        INDEX idx_close_date (CloseDate)
    )");

    // Create tasks table if it doesn't exist
    $db->exec("CREATE TABLE IF NOT EXISTS tasks (
        TaskID INT AUTO_INCREMENT PRIMARY KEY,
        Title VARCHAR(255) NOT NULL,
        Description TEXT,
        DueDate DATE,
        Priority ENUM('Low', 'Medium', 'High', 'Urgent') DEFAULT 'Medium',
        Status ENUM('Pending', 'In Progress', 'Completed', 'Cancelled') DEFAULT 'Pending',
        AssignedTo VARCHAR(100),
        RelatedTo ENUM('Lead', 'Deal', 'Customer', 'General') DEFAULT 'General',
        RelatedID INT,
        CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
        UpdatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_status (Status),
        INDEX idx_due_date (DueDate),
        INDEX idx_priority (Priority)
    )");

    // Insert sample leads data if table is empty
    $stmt = $db->query("SELECT COUNT(*) FROM leads");
    if ($stmt->fetchColumn() == 0) {
        $sampleLeads = [
            ['Alice', 'Johnson', 'alice@email.com', '555-2001', 'Tech Corp Inc', 'CTO', 50000.00, 'High', 'Qualified', 'Website', 'Interested in enterprise solution. Needs follow up.'],
            ['Bob', 'Smith', 'bob@email.com', '555-2002', 'Marketing Pro', 'Marketing Director', 25000.00, 'Normal', 'Contacted', 'Referral', 'Looking for marketing automation tools.'],
            ['Carol', 'Davis', 'carol@email.com', '555-2003', 'Retail Solutions', 'CEO', 75000.00, 'Urgent', 'Proposal', 'Social Media', 'Ready for proposal. High priority client.'],
            ['David', 'Wilson', 'david@email.com', '555-2004', 'Fashion Retail', 'Owner', 15000.00, 'Low', 'New', 'Cold Call', 'New lead from cold calling campaign.'],
            ['Eva', 'Brown', 'eva@email.com', '555-2005', 'Shoe Empire', 'Purchasing Manager', 35000.00, 'High', 'Negotiation', 'Event', 'Met at industry conference. Very interested.']
        ];

        $stmt = $db->prepare("INSERT INTO leads (FirstName, LastName, Email, Phone, CompanyName, JobTitle, PotentialValue, Priority, Status, Source, Notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($sampleLeads as $lead) {
            $stmt->execute($lead);
        }
    }

    // Insert sample deals data if table is empty
    $stmt = $db->query("SELECT COUNT(*) FROM deals");
    if ($stmt->fetchColumn() == 0) {
        $sampleDeals = [
            [1, 'Enterprise Software License', 50000.00, 'Proposal', 75, date('Y-m-d', strtotime('+30 days')), 'Waiting for client feedback on proposal.'],
            [2, 'Marketing Campaign', 25000.00, 'Qualification', 50, date('Y-m-d', strtotime('+45 days')), 'Need to schedule discovery call.'],
            [3, 'Retail System Implementation', 75000.00, 'Negotiation', 90, date('Y-m-d', strtotime('+15 days')), 'Finalizing contract terms.'],
            [4, 'E-commerce Platform', 15000.00, 'Prospecting', 25, date('Y-m-d', strtotime('+60 days')), 'Initial contact made.'],
            [5, 'Inventory Management', 35000.00, 'Qualification', 60, date('Y-m-d', strtotime('+30 days')), 'Assessing requirements.'],
            [null, 'Standalone Project A', 20000.00, 'Prospecting', 20, date('Y-m-d', strtotime('+90 days')), 'New business opportunity.'],
            [null, 'Service Contract B', 45000.00, 'Qualification', 40, date('Y-m-d', strtotime('+60 days')), 'Potential service agreement.']
        ];

        $stmt = $db->prepare("INSERT INTO deals (LeadID, DealName, DealValue, Stage, Probability, CloseDate, Notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($sampleDeals as $deal) {
            $stmt->execute($deal);
        }
    }

    // Insert sample tasks data if table is empty
    $stmt = $db->query("SELECT COUNT(*) FROM tasks");
    if ($stmt->fetchColumn() == 0) {
        $sampleTasks = [
            ['Follow up with Alice Johnson', 'Discuss proposal details and address concerns', date('Y-m-d', strtotime('+2 days')), 'High', 'Pending', 'John Doe', 'Lead', 1],
            ['Prepare contract for Retail Solutions', 'Draft service agreement and pricing', date('Y-m-d', strtotime('+5 days')), 'Urgent', 'In Progress', 'Jane Smith', 'Deal', 3],
            ['Schedule product demo', 'Organize demo for Marketing Pro team', date('Y-m-d', strtotime('+7 days')), 'Medium', 'Pending', 'Mike Johnson', 'Lead', 2],
            ['Update sales presentation', 'Include latest case studies and testimonials', date('Y-m-d', strtotime('+10 days')), 'Low', 'Pending', 'Sarah Wilson', 'General', NULL],
            ['Client meeting preparation', 'Prepare materials for quarterly review', date('Y-m-d', strtotime('+3 days')), 'High', 'Completed', 'John Doe', 'Lead', 1],
            ['Research competitor products', 'Analyze market trends and competitor offerings', date('Y-m-d', strtotime('+14 days')), 'Medium', 'Pending', 'Unassigned', 'General', NULL]
        ];

        $stmt = $db->prepare("INSERT INTO tasks (Title, Description, DueDate, Priority, Status, AssignedTo, RelatedTo, RelatedID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($sampleTasks as $task) {
            $stmt->execute($task);
        }
    }
}

// Initialize tables
initializeCRMTables($db);

// Handle AJAX requests for fetching data
if (isset($_GET['action']) && $_GET['action'] === 'get_lead_data') {
    $leadId = $_GET['lead_id'] ?? 0;
    if ($leadId > 0) {
        $stmt = $db->prepare("SELECT * FROM leads WHERE LeadID = ?");
        $stmt->execute([$leadId]);
        $lead = $stmt->fetch(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($lead);
        exit();
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'get_deal_data') {
    $dealId = $_GET['deal_id'] ?? 0;
    if ($dealId > 0) {
        $stmt = $db->prepare("SELECT * FROM deals WHERE DealID = ?");
        $stmt->execute([$dealId]);
        $deal = $stmt->fetch(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($deal);
        exit();
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'get_task_data') {
    $taskId = $_GET['task_id'] ?? 0;
    if ($taskId > 0) {
        $stmt = $db->prepare("SELECT * FROM tasks WHERE TaskID = ?");
        $stmt->execute([$taskId]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($task);
        exit();
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_lead'])) {
        // Add new lead
        $stmt = $db->prepare("INSERT INTO leads (FirstName, LastName, Email, Phone, CompanyName, JobTitle, PotentialValue, Priority, Status, Source, Notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['first_name'] ?? '',
            $_POST['last_name'] ?? '',
            $_POST['email'] ?? '',
            $_POST['phone'] ?? '',
            $_POST['company'] ?? '',
            $_POST['job_title'] ?? '',
            $_POST['potential_value'] ?? 0,
            $_POST['priority'] ?? 'Normal',
            $_POST['status'] ?? 'New',
            $_POST['source'] ?? 'Other',
            $_POST['notes'] ?? ''
        ]);

        // If deal name is provided, create a deal as well
        if (!empty($_POST['deal_name'])) {
            $leadId = $db->lastInsertId();
            $stmt = $db->prepare("INSERT INTO deals (LeadID, DealName, DealValue, Stage, Probability, CloseDate, Notes) VALUES (?, ?, ?, 'Prospecting', 10, DATE_ADD(NOW(), INTERVAL 30 DAY), ?)");
            $stmt->execute([
                $leadId,
                $_POST['deal_name'],
                $_POST['potential_value'] ?? 0,
                $_POST['notes'] ?? ''
            ]);
        }

        header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=customers&success=lead_added');
        exit();
    } elseif (isset($_POST['update_lead'])) {
        // Update existing lead
        $stmt = $db->prepare("UPDATE leads SET FirstName=?, LastName=?, Email=?, Phone=?, CompanyName=?, JobTitle=?, PotentialValue=?, Priority=?, Status=?, Source=?, Notes=? WHERE LeadID=?");
        $stmt->execute([
            $_POST['first_name'] ?? '',
            $_POST['last_name'] ?? '',
            $_POST['email'] ?? '',
            $_POST['phone'] ?? '',
            $_POST['company'] ?? '',
            $_POST['job_title'] ?? '',
            $_POST['potential_value'] ?? 0,
            $_POST['priority'] ?? 'Normal',
            $_POST['status'] ?? 'New',
            $_POST['source'] ?? 'Other',
            $_POST['notes'] ?? '',
            $_POST['lead_id']
        ]);
        header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=customers&success=lead_updated');
        exit();
    } elseif (isset($_POST['update_deal'])) {
        // Update deal
        $stmt = $db->prepare("UPDATE deals SET DealName=?, DealValue=?, Stage=?, Probability=?, CloseDate=?, Notes=? WHERE DealID=?");
        $stmt->execute([
            $_POST['deal_name'] ?? '',
            $_POST['deal_value'] ?? 0,
            $_POST['stage'] ?? 'Prospecting',
            $_POST['probability'] ?? 0,
            $_POST['close_date'] ?? date('Y-m-d'),
            $_POST['notes'] ?? '',
            $_POST['deal_id']
        ]);
        header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=deals&success=deal_updated');
        exit();
    } elseif (isset($_POST['update_task'])) {
        // Update task
        $stmt = $db->prepare("UPDATE tasks SET Title=?, Description=?, DueDate=?, Priority=?, Status=?, AssignedTo=? WHERE TaskID=?");
        $stmt->execute([
            $_POST['task_title'] ?? '',
            $_POST['task_description'] ?? '',
            $_POST['due_date'] ?? date('Y-m-d'),
            $_POST['task_priority'] ?? 'Medium',
            $_POST['task_status'] ?? 'Pending',
            $_POST['assigned_to'] ?? '',
            $_POST['task_id']
        ]);
        header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=tasks&success=task_updated');
        exit();
    } elseif (isset($_POST['assign_task'])) {
        // Assign task to user
        $stmt = $db->prepare("UPDATE tasks SET AssignedTo=? WHERE TaskID=?");
        $stmt->execute([
            $_POST['assign_to'] ?? '',
            $_POST['assign_task_id']
        ]);
        header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=tasks&success=task_assigned');
        exit();
    } elseif (isset($_POST['add_task'])) {
        // Add new task
        $stmt = $db->prepare("INSERT INTO tasks (Title, Description, DueDate, Priority, Status, AssignedTo, RelatedTo, RelatedID) VALUES (?, ?, ?, ?, 'Pending', ?, ?, ?)");
        $stmt->execute([
            $_POST['new_task_title'] ?? '',
            $_POST['new_task_description'] ?? '',
            $_POST['new_due_date'] ?? date('Y-m-d'),
            $_POST['new_task_priority'] ?? 'Medium',
            $_POST['new_assigned_to'] ?? '',
            $_POST['new_related_to'] ?? 'General',
            $_POST['new_related_id'] ?? NULL
        ]);
        header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=tasks&success=task_added');
        exit();
    }
}

// Handle delete actions
if (isset($_GET['delete'])) {
    $type = $_GET['delete'];
    $id = $_GET['id'] ?? 0;

    if ($type === 'lead' && $id > 0) {
        $stmt = $db->prepare("DELETE FROM leads WHERE LeadID = ?");
        $stmt->execute([$id]);
        header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=customers&success=lead_deleted');
        exit();
    } elseif ($type === 'task' && $id > 0) {
        $stmt = $db->prepare("DELETE FROM tasks WHERE TaskID = ?");
        $stmt->execute([$id]);
        header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=tasks&success=task_deleted');
        exit();
    }
}

// Handle export functionality
if (isset($_GET['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="crm_export.csv"');

    $output = fopen('php://output', 'w');

    $tab = $_GET['tab'] ?? 'customers';
    switch ($tab) {
        case 'deals':
            fputcsv($output, ['Deal ID', 'Deal Name', 'Contact', 'Company', 'Deal Value', 'Stage', 'Probability', 'Close Date']);
            $data = getTableData('deals', 1000, 0);
            foreach ($data as $row) {
                fputcsv($output, [
                    '#DEAL-' . str_pad($row['DealID'], 3, '0', STR_PAD_LEFT),
                    $row['DealName'],
                    ($row['FirstName'] ?? '') . ' ' . ($row['LastName'] ?? ''),
                    $row['CompanyName'] ?? '',
                    '₱' . number_format($row['DealValue'], 2),
                    $row['Stage'],
                    $row['Probability'] . '%',
                    date('M j, Y', strtotime($row['CloseDate']))
                ]);
            }
            break;

        case 'tasks':
            fputcsv($output, ['Task ID', 'Title', 'Description', 'Due Date', 'Priority', 'Status', 'Assigned To']);
            $data = getTableData('tasks', 1000, 0);
            foreach ($data as $row) {
                fputcsv($output, [
                    '#TASK-' . str_pad($row['TaskID'], 3, '0', STR_PAD_LEFT),
                    $row['Title'],
                    $row['Description'],
                    date('M j, Y', strtotime($row['DueDate'])),
                    $row['Priority'],
                    $row['Status'],
                    $row['AssignedTo']
                ]);
            }
            break;

        case 'performance':
            fputcsv($output, ['Contact', 'Email', 'Total Deals', 'Total Value', 'Avg Probability']);
            $data = getTableData('performance', 1000, 0);
            foreach ($data as $row) {
                fputcsv($output, [
                    ($row['FirstName'] ?? '') . ' ' . ($row['LastName'] ?? ''),
                    $row['Email'] ?? '',
                    $row['TotalDeals'] ?? 0,
                    '₱' . number_format($row['TotalValue'] ?? 0, 2),
                    round($row['AvgProbability'] ?? 0, 1) . '%'
                ]);
            }
            break;

        default: // customers
            fputcsv($output, ['Lead ID', 'Name', 'Email', 'Company', 'Deal Value', 'Priority', 'Status']);
            $data = getTableData('customers', 1000, 0);
            foreach ($data as $row) {
                fputcsv($output, [
                    '#LEAD-' . str_pad($row['LeadID'], 3, '0', STR_PAD_LEFT),
                    $row['FirstName'] . ' ' . $row['LastName'],
                    $row['Email'],
                    $row['CompanyName'],
                    '₱' . number_format($row['PotentialValue'], 2),
                    $row['Priority'],
                    $row['Status']
                ]);
            }
            break;
    }

    fclose($output);
    exit();
}

// Get current active tab
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'customers';

// Function to get data based on active tab
function getTableData($tab, $limit = 5, $offset = 0)
{
    global $db;

    // Apply filters if set
    $whereConditions = [];
    $params = [];

    if (isset($_GET['filter'])) {
        switch ($_GET['filter']) {
            case 'high':
                $whereConditions[] = "Priority = 'High' OR Priority = 'Urgent'";
                break;
            case 'active':
                if ($tab === 'deals') {
                    $whereConditions[] = "Stage NOT IN ('Closed Won', 'Closed Lost')";
                } elseif ($tab === 'tasks') {
                    $whereConditions[] = "Status NOT IN ('Completed', 'Cancelled')";
                } else {
                    $whereConditions[] = "Status NOT IN ('Won', 'Lost')";
                }
                break;
            case 'recent':
                $whereConditions[] = "CreatedAt >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
        }
    }

    switch ($tab) {
        case 'deals':
            $sql = "SELECT d.*, l.FirstName, l.LastName, l.Email, l.CompanyName 
                    FROM deals d 
                    LEFT JOIN leads l ON d.LeadID = l.LeadID";
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            $sql .= " ORDER BY ";
            if (isset($_GET['sort'])) {
                switch ($_GET['sort']) {
                    case 'oldest':
                        $sql .= "d.CreatedAt ASC";
                        break;
                    case 'value':
                        $sql .= "d.DealValue DESC";
                        break;
                    case 'probability':
                        $sql .= "d.Probability DESC";
                        break;
                    default:
                        $sql .= "d.CreatedAt DESC";
                        break;
                }
            } else {
                $sql .= "d.CreatedAt DESC";
            }
            $sql .= " LIMIT ? OFFSET ?";
            $params = array_merge($params, [(int)$limit, (int)$offset]);
            break;

        case 'tasks':
            $sql = "SELECT * FROM tasks";
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            $sql .= " ORDER BY ";
            if (isset($_GET['sort'])) {
                switch ($_GET['sort']) {
                    case 'oldest':
                        $sql .= "CreatedAt ASC";
                        break;
                    case 'priority':
                        $sql .= "FIELD(Priority, 'Urgent', 'High', 'Medium', 'Low')";
                        break;
                    case 'due_date':
                        $sql .= "DueDate ASC";
                        break;
                    default:
                        $sql .= "CreatedAt DESC";
                        break;
                }
            } else {
                $sql .= "FIELD(Priority, 'Urgent', 'High', 'Medium', 'Low'), DueDate ASC";
            }
            $sql .= " LIMIT ? OFFSET ?";
            $params = array_merge($params, [(int)$limit, (int)$offset]);
            break;

        case 'performance':
            $sql = "SELECT 
                        l.LeadID,
                        l.FirstName,
                        l.LastName,
                        l.Email,
                        COUNT(d.DealID) as TotalDeals,
                        COALESCE(SUM(d.DealValue), 0) as TotalValue,
                        COALESCE(AVG(d.Probability), 0) as AvgProbability
                    FROM leads l
                    LEFT JOIN deals d ON l.LeadID = d.LeadID
                    GROUP BY l.LeadID, l.FirstName, l.LastName, l.Email
                    ORDER BY ";
            if (isset($_GET['sort'])) {
                switch ($_GET['sort']) {
                    case 'deals':
                        $sql .= "TotalDeals DESC";
                        break;
                    case 'value':
                        $sql .= "TotalValue DESC";
                        break;
                    case 'probability':
                        $sql .= "AvgProbability DESC";
                        break;
                    default:
                        $sql .= "TotalValue DESC";
                        break;
                }
            } else {
                $sql .= "TotalValue DESC";
            }
            $sql .= " LIMIT ? OFFSET ?";
            $params = [(int)$limit, (int)$offset];
            break;

        case 'customers':
        default:
            $sql = "SELECT 
                        LeadID,
                        FirstName,
                        LastName,
                        Email,
                        Phone,
                        CompanyName,
                        JobTitle,
                        PotentialValue,
                        Priority,
                        Status,
                        Source,
                        Notes,
                        CreatedAt
                    FROM leads";
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            $sql .= " ORDER BY ";
            if (isset($_GET['sort'])) {
                switch ($_GET['sort']) {
                    case 'oldest':
                        $sql .= "CreatedAt ASC";
                        break;
                    case 'value':
                        $sql .= "PotentialValue DESC";
                        break;
                    case 'priority':
                        $sql .= "FIELD(Priority, 'Urgent', 'High', 'Normal', 'Low')";
                        break;
                    default:
                        $sql .= "CreatedAt DESC";
                        break;
                }
            } else {
                $sql .= "CreatedAt DESC";
            }
            $sql .= " LIMIT ? OFFSET ?";
            $params = array_merge($params, [(int)$limit, (int)$offset]);
            break;
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTotalCount($tab)
{
    global $db;

    $whereConditions = [];

    if (isset($_GET['filter'])) {
        switch ($_GET['filter']) {
            case 'high':
                $whereConditions[] = "Priority = 'High' OR Priority = 'Urgent'";
                break;
            case 'active':
                if ($tab === 'deals') {
                    $whereConditions[] = "Stage NOT IN ('Closed Won', 'Closed Lost')";
                } elseif ($tab === 'tasks') {
                    $whereConditions[] = "Status NOT IN ('Completed', 'Cancelled')";
                } else {
                    $whereConditions[] = "Status NOT IN ('Won', 'Lost')";
                }
                break;
            case 'recent':
                $whereConditions[] = "CreatedAt >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
        }
    }

    switch ($tab) {
        case 'deals':
            $sql = "SELECT COUNT(*) as total FROM deals";
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            break;

        case 'tasks':
            $sql = "SELECT COUNT(*) as total FROM tasks";
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            break;

        case 'performance':
            $sql = "SELECT COUNT(DISTINCT l.LeadID) as total FROM leads l LEFT JOIN deals d ON l.LeadID = d.LeadID WHERE d.DealID IS NOT NULL";
            break;

        case 'customers':
        default:
            $sql = "SELECT COUNT(*) as total FROM leads";
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            break;
    }

    $res = $db->query($sql)->fetch();
    return (int)($res['total'] ?? 0);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Dashboard - Enterprise Edition</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./styles/crmGlobalStyles.css">
    <style>
        /* Modal Styles - FIXED CENTERING */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .modal {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }
        
        .modal-overlay.active .modal {
            transform: scale(1);
        }
        
        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .modal-title {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin: 0;
            flex: 1;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #6b7280;
            padding: 4px;
        }
        
        .close-btn:hover {
            color: #374151;
        }
        
        .modal-body {
            padding: 24px;
        }
        
        .modal-footer {
            padding: 20px 24px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
        
        .btn-danger {
            background: #dc2626;
            color: white;
        }
        
        .btn-danger:hover {
            background: #b91c1c;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .lead-details {
            background: #f8fafc;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #3b82f6;
        }

        .detail-row {
            display: flex;
            margin-bottom: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .detail-label {
            font-weight: 600;
            width: 140px;
            color: #64748b;
            flex-shrink: 0;
        }

        .detail-value {
            flex: 1;
            color: #1e293b;
        }

        .table-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .filter-dropdown,
        .sort-dropdown,
        .settings-dropdown {
            position: relative;
            display: inline-block;
        }

        .filter-content,
        .sort-content,
        .settings-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 200px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 15px;
            z-index: 100;
            right: 0;
            top: 100%;
        }

        .filter-content.show,
        .sort-content.show,
        .settings-content.show {
            display: block;
        }

        .dropdown-item {
            display: block;
            padding: 8px 12px;
            margin: 4px 0;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
            text-decoration: none;
            color: inherit;
        }

        .dropdown-item:hover {
            background-color: #f1f5f9;
        }

        .dropdown-item.active {
            background-color: #3b82f6;
            color: white;
        }

        .success-message {
            background-color: #d1fae5;
            border: 1px solid #10b981;
            color: #065f46;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .delete-btn {
            background-color: #ef4444;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .delete-btn:hover {
            background-color: #dc2626;
        }

        .assign-btn {
            background-color: #8b5cf6;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .assign-btn:hover {
            background-color: #7c3aed;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .add-task-btn {
            background-color: #10b981;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            margin-left: 10px;
        }

        .add-task-btn:hover {
            background-color: #059669;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }

        /* Loading spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive design for modals */
        @media (max-width: 768px) {
            .modal {
                width: 95%;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .modal {
                width: 95%;
            }

            .form-actions {
                flex-direction: column;
            }

            .form-actions button {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="navbar-inner">
            <div class="brand">
                <div class="brand-icon">C</div>
                <span>CRM Enterprise</span>
            </div>
            <ul class="nav-menu">
                <li><a href="#" class="active">Dashboard</a></li>
                <li><a href="./customerProfile.php">Customer Profiles</a></li>
                <li><a href="./loyaltyProgram.php">Loyalty Program</a></li>
                <li><a href="./customerSupport.php">Customer Support</a></li>
                <li><a href="./reportsManagement.php">Reports & Analytics</a></li>
            </ul>
            <div class="nav-right">
                <div class="search-wrapper">
                    <span class="search-icon">🔍</span>
                    <input type="text" class="search-box" placeholder="Search anything..." id="globalSearch">
                </div>
                <button class="notification-btn">
                    🔔
                    <span class="notification-badge">5</span>
                </button>
                <a href="./crmProfile.php">
                    <div class="user-avatar">JD</div>
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="breadcrumb">
                    <span>Home</span>
                    <span class="breadcrumb-separator">/</span>
                    <span>Dashboard</span>
                </div>
                <h1 class="page-title">CRM Dashboard</h1>
                <p class="page-subtitle">Track and manage your sales pipeline in real-time</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-secondary" onclick="exportData()">
                    <span>📊</span>
                    <span>Export</span>
                </button>
                <button class="btn btn-primary" onclick="openModal('addLeadModal')">
                    <span>+</span>
                    <span>Add New Lead</span>
                </button>
                <?php if ($activeTab === 'tasks'): ?>
                    <button class="add-task-btn" onclick="openModal('addTaskModal')">
                        <span>+</span>
                        <span>Add Task</span>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <?php
                switch ($_GET['success']) {
                    case 'lead_added':
                        echo 'Lead added successfully!';
                        break;
                    case 'lead_updated':
                        echo 'Lead updated successfully!';
                        break;
                    case 'lead_deleted':
                        echo 'Lead deleted successfully!';
                        break;
                    case 'deal_updated':
                        echo 'Deal updated successfully!';
                        break;
                    case 'task_updated':
                        echo 'Task updated successfully!';
                        break;
                    case 'task_assigned':
                        echo 'Task assigned successfully!';
                        break;
                    case 'task_added':
                        echo 'Task added successfully!';
                        break;
                    case 'task_deleted':
                        echo 'Task deleted successfully!';
                        break;
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="stats-grid">
            <?php
            class DashboardStats
            {
                private $db;

                public function __construct()
                {
                    global $db;
                    $this->db = $db;
                }

                public function getTotalLeads()
                {
                    $sql = "SELECT 
                            COUNT(*) as total,
                            COUNT(CASE WHEN CreatedAt >= DATE_SUB(NOW(), INTERVAL 1 MONTH) THEN 1 END) as last_month
                           FROM leads";
                    $result = $this->db->query($sql)->fetch();
                    $total = (int)($result['total'] ?? 0);
                    $last = (int)($result['last_month'] ?? 0);
                    $lastMonthPercentage = $last > 0 && $total > 0
                        ? round(($last / $total) * 100, 1)
                        : 0;
                    return [
                        'value' => number_format($total),
                        'trend' => ($lastMonthPercentage > 0 ? '+' : '') . $lastMonthPercentage . '%'
                    ];
                }

                public function getQualifiedLeads()
                {
                    $sql = "SELECT 
                            COUNT(*) as total,
                            COUNT(CASE WHEN UpdatedAt >= DATE_SUB(NOW(), INTERVAL 1 MONTH) THEN 1 END) as last_month
                           FROM leads 
                           WHERE Status IN ('Qualified', 'Proposal', 'Negotiation')";
                    $result = $this->db->query($sql)->fetch();
                    $total = (int)($result['total'] ?? 0);
                    $last = (int)($result['last_month'] ?? 0);
                    $lastMonthPercentage = $last > 0 && $total > 0
                        ? round(($last / $total) * 100, 1)
                        : 0;
                    return [
                        'value' => number_format($total),
                        'trend' => ($lastMonthPercentage > 0 ? '+' : '') . $lastMonthPercentage . '%'
                    ];
                }

                public function getPipelineValue()
                {
                    $sql = "SELECT 
                            COALESCE(SUM(PotentialValue), 0) as total,
                            COALESCE(SUM(CASE WHEN CreatedAt >= DATE_SUB(NOW(), INTERVAL 1 MONTH) THEN PotentialValue ELSE 0 END), 0) as last_month
                           FROM leads 
                           WHERE Status NOT IN ('Lost', 'Won')";
                    $result = $this->db->query($sql)->fetch();
                    $total = (float)($result['total'] ?? 0);
                    $last = (float)($result['last_month'] ?? 0);
                    $lastMonthPercentage = $last > 0 && $total > 0
                        ? round(($last / $total) * 100, 1)
                        : 0;
                    return [
                        'value' => '₱' . number_format($total, 2),
                        'trend' => ($lastMonthPercentage > 0 ? '+' : '') . $lastMonthPercentage . '%'
                    ];
                }

                public function getConversionRate()
                {
                    $sql = "SELECT 
                            COALESCE((COUNT(CASE WHEN Status = 'Won' THEN 1 END) * 100.0 / NULLIF(COUNT(*), 0)), 0) as rate,
                            COALESCE((COUNT(CASE WHEN Status = 'Won' AND UpdatedAt >= DATE_SUB(NOW(), INTERVAL 3 MONTH) THEN 1 END) * 100.0 / 
                             NULLIF(COUNT(CASE WHEN UpdatedAt >= DATE_SUB(NOW(), INTERVAL 3 MONTH) THEN 1 END), 0)), 0) as last_quarter
                           FROM leads 
                           WHERE Status IN ('Won', 'Lost') OR Status IS NULL";
                    $result = $this->db->query($sql)->fetch();
                    $rate = (float)($result['rate'] ?? 0);
                    $last_quarter = (float)($result['last_quarter'] ?? 0);
                    $trend = $last_quarter - $rate;
                    return [
                        'value' => round($rate, 1) . '%',
                        'trend' => ($trend > 0 ? '+' : '') . round($trend, 1) . '%'
                    ];
                }
            }

            $dashboard = new DashboardStats();
            $totalLeads = $dashboard->getTotalLeads();
            $qualifiedLeads = $dashboard->getQualifiedLeads();
            $pipelineValue = $dashboard->getPipelineValue();
            $conversionRate = $dashboard->getConversionRate();

            $stats = [
                [
                    'icon' => '👥',
                    'value' => $totalLeads['value'],
                    'label' => 'Total Leads',
                    'sublabel' => 'Active in pipeline',
                    'trend' => $totalLeads['trend'],
                    'trend_dir' => strpos($totalLeads['trend'], '+') !== false ? 'up' : 'down',
                    'color' => '#dbeafe'
                ],
                [
                    'icon' => '✅',
                    'value' => $qualifiedLeads['value'],
                    'label' => 'Qualified Leads',
                    'sublabel' => 'Ready for conversion',
                    'trend' => $qualifiedLeads['trend'],
                    'trend_dir' => strpos($qualifiedLeads['trend'], '+') !== false ? 'up' : 'down',
                    'color' => '#d1fae5'
                ],
                [
                    'icon' => '💰',
                    'value' => $pipelineValue['value'],
                    'label' => 'Pipeline Value',
                    'sublabel' => 'Potential revenue',
                    'trend' => $pipelineValue['trend'],
                    'trend_dir' => strpos($pipelineValue['trend'], '+') !== false ? 'up' : 'down',
                    'color' => '#fef3c7'
                ],
                [
                    'icon' => '📈',
                    'value' => $conversionRate['value'],
                    'label' => 'Conversion Rate',
                    'sublabel' => 'This quarter',
                    'trend' => $conversionRate['trend'],
                    'trend_dir' => strpos($conversionRate['trend'], '+') !== false ? 'up' : 'down',
                    'color' => '#ddd6fe'
                ]
            ];

            foreach ($stats as $stat) {
                echo '<div class="stat-card">';
                echo '<div class="stat-header">';
                echo '<div class="stat-icon" style="background: ' . $stat['color'] . ';">' . $stat['icon'] . '</div>';
                echo '<div class="stat-trend ' . $stat['trend_dir'] . '">';
                echo '<span>' . ($stat['trend_dir'] === 'up' ? '↑' : '↓') . '</span>';
                echo '<span>' . $stat['trend'] . '</span>';
                echo '</div>';
                echo '</div>';
                echo '<div class="stat-body">';
                echo '<div class="stat-value">' . $stat['value'] . '</div>';
                echo '<div class="stat-label">' . $stat['label'] . '</div>';
                echo '<div class="stat-sublabel">' . $stat['sublabel'] . '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>

        <div class="tabs-wrapper">
            <div class="tabs">
                <button class="tab <?php echo $activeTab === 'customers' ? 'active' : ''; ?>" onclick="switchTab('customers')">Recent Customers</button>
                <button class="tab <?php echo $activeTab === 'deals' ? 'active' : ''; ?>" onclick="switchTab('deals')">Active Deals</button>
                <button class="tab <?php echo $activeTab === 'tasks' ? 'active' : ''; ?>" onclick="switchTab('tasks')">Tasks & Follow-ups</button>
                <button class="tab <?php echo $activeTab === 'performance' ? 'active' : ''; ?>" onclick="switchTab('performance')">Performance</button>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h2 class="section-title">
                    <?php
                    switch ($activeTab) {
                        case 'deals':
                            echo 'Active Deals Overview';
                            break;
                        case 'tasks':
                            echo 'Tasks & Follow-ups';
                            break;
                        case 'performance':
                            echo 'Sales Performance';
                            break;
                        default:
                            echo 'Recent Customers Overview';
                            break;
                    }
                    ?>
                </h2>
                <div class="card-actions">
                    <div class="table-controls">
                        <div class="filter-dropdown">
                            <button class="icon-btn" title="Filter" onclick="toggleFilter()">🔽</button>
                            <div class="filter-content" id="filterContent">
                                <?php
                                $currentFilter = $_GET['filter'] ?? 'all';
                                $filters = [
                                    'all' => 'All',
                                    'high' => 'High Priority',
                                    'active' => 'Active Only',
                                    'recent' => 'Recent'
                                ];
                                foreach ($filters as $key => $label) {
                                    $active = $currentFilter === $key ? 'active' : '';
                                    echo '<a href="?tab=' . $activeTab . '&filter=' . $key . '&sort=' . ($_GET['sort'] ?? '') . '" class="dropdown-item ' . $active . '">' . $label . '</a>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="sort-dropdown">
                            <button class="icon-btn" title="Sort" onclick="toggleSort()">⇅</button>
                            <div class="sort-content" id="sortContent">
                                <?php
                                $currentSort = $_GET['sort'] ?? 'newest';
                                $sorts = [
                                    'newest' => 'Newest First',
                                    'oldest' => 'Oldest First',
                                    'value' => 'By Value',
                                    'priority' => 'By Priority'
                                ];
                                if ($activeTab === 'deals') {
                                    $sorts['probability'] = 'By Probability';
                                } elseif ($activeTab === 'tasks') {
                                    $sorts['due_date'] = 'By Due Date';
                                } elseif ($activeTab === 'performance') {
                                    $sorts = [
                                        'value' => 'By Total Value',
                                        'deals' => 'By Deal Count',
                                        'probability' => 'By Probability'
                                    ];
                                }
                                foreach ($sorts as $key => $label) {
                                    $active = $currentSort === $key ? 'active' : '';
                                    echo '<a href="?tab=' . $activeTab . '&sort=' . $key . '&filter=' . ($_GET['filter'] ?? '') . '" class="dropdown-item ' . $active . '">' . $label . '</a>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="settings-dropdown">
                            <button class="icon-btn" title="Settings" onclick="toggleSettings()">⚙️</button>
                            <div class="settings-content" id="settingsContent">
                                <div class="dropdown-item" onclick="refreshData()">Refresh Data</div>
                                <div class="dropdown-item" onclick="exportData()">Export Data</div>
                                <div class="dropdown-item" onclick="resetView()">Reset View</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <?php
                            switch ($activeTab) {
                                case 'deals':
                                    echo '<th>Deal ID</th>';
                                    echo '<th>Deal Name</th>';
                                    echo '<th>Contact</th>';
                                    echo '<th>Company</th>';
                                    echo '<th>Deal Value</th>';
                                    echo '<th>Stage</th>';
                                    echo '<th>Probability</th>';
                                    echo '<th>Close Date</th>';
                                    echo '<th>Actions</th>';
                                    break;

                                case 'tasks':
                                    echo '<th>Task ID</th>';
                                    echo '<th>Title</th>';
                                    echo '<th>Description</th>';
                                    echo '<th>Due Date</th>';
                                    echo '<th>Priority</th>';
                                    echo '<th>Status</th>';
                                    echo '<th>Assigned To</th>';
                                    echo '<th>Actions</th>';
                                    break;

                                case 'performance':
                                    echo '<th>Contact</th>';
                                    echo '<th>Email</th>';
                                    echo '<th>Total Deals</th>';
                                    echo '<th>Total Value</th>';
                                    echo '<th>Avg Probability</th>';
                                    echo '<th>Performance Score</th>';
                                    break;

                                default: // customers
                                    echo '<th>Lead ID</th>';
                                    echo '<th>Contact</th>';
                                    echo '<th>Company</th>';
                                    echo '<th>Deal Value</th>';
                                    echo '<th>Priority</th>';
                                    echo '<th>Status</th>';
                                    echo '<th>Actions</th>';
                                    break;
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                        $perPage = 5;
                        $offset = ($page - 1) * $perPage;

                        try {
                            $tableData = getTableData($activeTab, $perPage, $offset);
                            $totalCount = getTotalCount($activeTab);

                            if (empty($tableData)) {
                                echo '<tr><td colspan="8" style="text-align: center; padding: 20px; color: #6b7280;">No data found</td></tr>';
                            } else {
                                foreach ($tableData as $row) {
                                    switch ($activeTab) {
                                        case 'deals':
                                            $initials = strtoupper(substr($row['FirstName'] ?? '', 0, 1) . substr($row['LastName'] ?? '', 0, 1));
                                            $contactName = ($row['FirstName'] || $row['LastName']) ? ($row['FirstName'] ?? '') . ' ' . ($row['LastName'] ?? '') : 'No Contact';
                                            $companyName = $row['CompanyName'] ?? 'Standalone Deal';
                                            echo '<tr>';
                                            echo '<td><span class="lead-id">#DEAL-' . str_pad($row['DealID'], 3, '0', STR_PAD_LEFT) . '</span></td>';
                                            echo '<td><strong>' . htmlspecialchars($row['DealName']) . '</strong></td>';
                                            echo '<td>';
                                            echo '<div class="lead-name">';
                                            echo '<div class="lead-avatar">' . ($initials ?: '?') . '</div>';
                                            echo '<div class="lead-info">';
                                            echo '<div class="lead-info-name">' . htmlspecialchars($contactName) . '</div>';
                                            echo '<div class="lead-info-email">' . htmlspecialchars($row['Email'] ?? '') . '</div>';
                                            echo '</div>';
                                            echo '</div>';
                                            echo '</td>';
                                            echo '<td>' . htmlspecialchars($companyName) . '</td>';
                                            echo '<td><span class="value-cell">₱' . number_format($row['DealValue'], 2) . '</span></td>';
                                            echo '<td><span class="status-badge status-' . strtolower(str_replace(' ', '-', $row['Stage'])) . '">' . $row['Stage'] . '</span></td>';
                                            echo '<td><span class="priority-badge">' . $row['Probability'] . '%</span></td>';
                                            echo '<td>' . date('M j, Y', strtotime($row['CloseDate'])) . '</td>';
                                            echo '<td>';
                                            echo '<div class="action-buttons">';
                                            echo '<button class="edit-btn" onclick="editDeal(' . $row['DealID'] . ')">Edit</button>';
                                            echo '</div>';
                                            echo '</td>';
                                            echo '</tr>';
                                            break;

                                        case 'tasks':
                                            $priorityClass = 'priority-' . strtolower($row['Priority']);
                                            $statusClass = 'status-' . strtolower($row['Status']);
                                            echo '<tr>';
                                            echo '<td><span class="lead-id">#TASK-' . str_pad($row['TaskID'], 3, '0', STR_PAD_LEFT) . '</span></td>';
                                            echo '<td><strong>' . htmlspecialchars($row['Title']) . '</strong></td>';
                                            echo '<td>' . (strlen($row['Description']) > 50 ? htmlspecialchars(substr($row['Description'], 0, 50)) . '...' : htmlspecialchars($row['Description'])) . '</td>';
                                            echo '<td>' . date('M j, Y', strtotime($row['DueDate'])) . '</td>';
                                            echo '<td><span class="priority-badge ' . $priorityClass . '">' . $row['Priority'] . '</span></td>';
                                            echo '<td><span class="status-badge ' . $statusClass . '">' . $row['Status'] . '</span></td>';
                                            echo '<td>' . htmlspecialchars($row['AssignedTo']) . '</td>';
                                            echo '<td>';
                                            echo '<div class="action-buttons">';
                                            if ($row['AssignedTo'] === 'Unassigned' || empty($row['AssignedTo'])) {
                                                echo '<button class="assign-btn" onclick="assignTask(' . $row['TaskID'] . ')">Assign</button>';
                                            }
                                            echo '<button class="edit-btn" onclick="editTask(' . $row['TaskID'] . ')">Edit</button>';
                                            echo '<button class="delete-btn" onclick="deleteTask(' . $row['TaskID'] . ')">Delete</button>';
                                            echo '</div>';
                                            echo '</td>';
                                            echo '</tr>';
                                            break;

                                        case 'performance':
                                            $leadId = $row['LeadID'] ?? 0;
                                            $totalValue = $row['TotalValue'] ?? 0;
                                            $totalDeals = $row['TotalDeals'] ?? 0;
                                            $avgProbability = $row['AvgProbability'] ?? 0;

                                            // Calculate performance score
                                            $performanceScore = min(100, round(($totalValue / 100000) * 40 + ($totalDeals * 10) + ($avgProbability * 0.5)));

                                            echo '<tr>';
                                            echo '<td>';
                                            echo '<div class="lead-name">';
                                            echo '<div class="lead-avatar">' . strtoupper(substr($row['FirstName'] ?? '', 0, 1) . substr($row['LastName'] ?? '', 0, 1)) . '</div>';
                                            echo '<div class="lead-info">';
                                            echo '<div class="lead-info-name">' . htmlspecialchars($row['FirstName'] ?? '') . ' ' . htmlspecialchars($row['LastName'] ?? '') . '</div>';
                                            echo '<div class="lead-info-email">' . htmlspecialchars($row['Email'] ?? '') . '</div>';
                                            echo '</div>';
                                            echo '</div>';
                                            echo '</td>';
                                            echo '<td>' . htmlspecialchars($row['Email'] ?? '') . '</td>';
                                            echo '<td><strong>' . $totalDeals . '</strong></td>';
                                            echo '<td><span class="value-cell">₱' . number_format($totalValue, 2) . '</span></td>';
                                            echo '<td><span class="priority-badge">' . round($avgProbability, 1) . '%</span></td>';
                                            echo '<td>';
                                            echo '<div class="performance-score">';
                                            echo '<div style="background: #e5e7eb; border-radius: 10px; height: 20px; width: 100%; margin: 5px 0;">';
                                            echo '<div style="background: ' . ($performanceScore >= 80 ? '#10b981' : ($performanceScore >= 60 ? '#f59e0b' : '#ef4444')) . '; border-radius: 10px; height: 100%; width: ' . $performanceScore . '%;"></div>';
                                            echo '</div>';
                                            echo '<span style="font-size: 12px; color: #6b7280;">' . $performanceScore . '/100</span>';
                                            echo '</div>';
                                            echo '</td>';
                                            echo '</tr>';
                                            break;

                                        default: // customers
                                            $names = array_filter(explode(' ', $row['FirstName'] . ' ' . $row['LastName']));
                                            $initials = '';
                                            foreach ($names as $n) {
                                                $initials .= strtoupper(substr($n, 0, 1));
                                            }

                                            $statusClass = 'status-' . strtolower($row['Status']);
                                            $priorityClass = 'priority-' . strtolower($row['Priority']);

                                            echo '<tr>';
                                            echo '<td><span class="lead-id">#LEAD-' . str_pad($row['LeadID'], 3, '0', STR_PAD_LEFT) . '</span></td>';
                                            echo '<td>';
                                            echo '<div class="lead-name">';
                                            echo '<div class="lead-avatar">' . $initials . '</div>';
                                            echo '<div class="lead-info">';
                                            echo '<div class="lead-info-name">' . htmlspecialchars($row['FirstName']) . ' ' . htmlspecialchars($row['LastName']) . '</div>';
                                            echo '<div class="lead-info-email">' . htmlspecialchars($row['Email']) . '</div>';
                                            echo '</div>';
                                            echo '</div>';
                                            echo '</td>';
                                            echo '<td>';
                                            echo '<div class="company-cell">';
                                            echo '<div class="company-icon">🏢</div>';
                                            echo '<span>' . htmlspecialchars($row['CompanyName']) . '</span>';
                                            echo '</div>';
                                            echo '</td>';
                                            echo '<td><span class="value-cell">₱' . number_format($row['PotentialValue'], 2) . '</span></td>';
                                            echo '<td><span class="priority-badge ' . $priorityClass . '">' . $row['Priority'] . '</span></td>';
                                            echo '<td><span class="status-badge ' . $statusClass . '">' . strtoupper($row['Status']) . '</span></td>';
                                            echo '<td>';
                                            echo '<div class="action-buttons">';
                                            echo '<button class="view-btn" onclick="viewLead(' . $row['LeadID'] . ')">View</button>';
                                            echo '<button class="edit-btn" onclick="editLead(' . $row['LeadID'] . ')">Edit</button>';
                                            echo '<button class="delete-btn" onclick="deleteLead(' . $row['LeadID'] . ')">Delete</button>';
                                            echo '</div>';
                                            echo '</td>';
                                            echo '</tr>';
                                            break;
                                    }
                                }
                            }
                        } catch (Exception $e) {
                            echo '<tr><td colspan="8" class="error-message">An error occurred while loading the data. Please try again later.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="table-footer">
                <?php
                $start = $offset + 1;
                $end = min($offset + count($tableData), $totalCount);
                ?>
                <div class="showing-text">
                    Showing <strong><?php echo $start ?>-<?php echo $end ?></strong> of <strong><?php echo number_format($totalCount) ?></strong> records
                </div>
                <div class="pagination">
                    <?php
                    $totalPages = max(1, (int)ceil($totalCount / $perPage));
                    $preserve = $_GET;
                    $preserve['tab'] = $activeTab;

                    // Previous link
                    if ($page > 1) {
                        $prevPage = $page - 1;
                        $preserve['page'] = $prevPage;
                        $prevUrl = '?' . http_build_query($preserve);
                        echo '<a class="page-btn" href="' . $prevUrl . '">‹</a>';
                    }

                    // Page numbers
                    $maxPages = 5;
                    $startPage = max(1, min($page - floor($maxPages / 2), max(1, $totalPages - $maxPages + 1)));
                    $endPage = min($startPage + $maxPages - 1, $totalPages);

                    for ($i = $startPage; $i <= $endPage; $i++):
                        $preserve['page'] = $i;
                        $url = '?' . http_build_query($preserve);
                        $active = ($i == $page) ? 'active' : '';
                    ?>
                        <a href="<?php echo $url ?>" class="page-btn <?php echo $active ?>"><?php echo $i ?></a>
                    <?php endfor; ?>

                    <?php
                    // Next link
                    if ($page < $totalPages) {
                        $nextPage = $page + 1;
                        $preserve['page'] = $nextPage;
                        $nextUrl = '?' . http_build_query($preserve);
                        echo '<a class="page-btn" href="' . $nextUrl . '">›</a>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Lead Modal -->
    <div class="modal-overlay" id="addLeadModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title" id="leadModalTitle">Add New Lead</h3>
                <button class="close-btn" onclick="closeModal('addLeadModal')">✕</button>
            </div>
            <div class="modal-body">
                <form id="leadForm" method="POST">
                    <input type="hidden" name="lead_id" id="lead_id">
                    <input type="hidden" name="add_lead" id="add_lead" value="1">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>

                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email">
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone">
                        </div>

                        <div class="form-group">
                            <label for="company">Company</label>
                            <input type="text" id="company" name="company">
                        </div>

                        <div class="form-group">
                            <label for="job_title">Job Title</label>
                            <input type="text" id="job_title" name="job_title">
                        </div>

                        <div class="form-group full-width">
                            <label for="deal_name">Deal Name (Optional)</label>
                            <input type="text" id="deal_name" name="deal_name" placeholder="e.g., Enterprise Software License">
                        </div>

                        <div class="form-group">
                            <label for="potential_value">Potential Value (₱)</label>
                            <input type="number" id="potential_value" name="potential_value" step="0.01" min="0" value="0">
                        </div>

                        <div class="form-group">
                            <label for="priority">Priority</label>
                            <select id="priority" name="priority">
                                <option value="Low">Low</option>
                                <option value="Normal" selected>Normal</option>
                                <option value="High">High</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="New" selected>New</option>
                                <option value="Contacted">Contacted</option>
                                <option value="Qualified">Qualified</option>
                                <option value="Proposal">Proposal</option>
                                <option value="Negotiation">Negotiation</option>
                                <option value="Won">Won</option>
                                <option value="Lost">Lost</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="source">Source</label>
                            <select id="source" name="source">
                                <option value="Website">Website</option>
                                <option value="Referral">Referral</option>
                                <option value="Social Media">Social Media</option>
                                <option value="Cold Call">Cold Call</option>
                                <option value="Event">Event</option>
                                <option value="Other" selected>Other</option>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label for="notes">Notes</label>
                            <textarea id="notes" name="notes" rows="4" placeholder="Add any notes about this lead..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addLeadModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" onclick="document.getElementById('leadForm').submit()">Save Lead</button>
            </div>
        </div>
    </div>

    <!-- Edit Deal Modal -->
    <div class="modal-overlay" id="dealModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Edit Deal</h3>
                <button class="close-btn" onclick="closeModal('dealModal')">✕</button>
            </div>
            <div class="modal-body">
                <form id="dealForm" method="POST">
                    <input type="hidden" name="deal_id" id="edit_deal_id">
                    <input type="hidden" name="update_deal" value="1">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="edit_deal_name">Deal Name</label>
                            <input type="text" id="edit_deal_name" name="deal_name" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_deal_value">Deal Value (₱)</label>
                            <input type="number" id="edit_deal_value" name="deal_value" step="0.01" min="0" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_stage">Stage</label>
                            <select id="edit_stage" name="stage">
                                <option value="Prospecting">Prospecting</option>
                                <option value="Qualification">Qualification</option>
                                <option value="Proposal">Proposal</option>
                                <option value="Negotiation">Negotiation</option>
                                <option value="Closed Won">Closed Won</option>
                                <option value="Closed Lost">Closed Lost</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_probability">Probability (%)</label>
                            <input type="number" id="edit_probability" name="probability" min="0" max="100" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_close_date">Close Date</label>
                            <input type="date" id="edit_close_date" name="close_date" required>
                        </div>

                        <div class="form-group full-width">
                            <label for="edit_deal_notes">Notes</label>
                            <textarea id="edit_deal_notes" name="notes" rows="4"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('dealModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" onclick="document.getElementById('dealForm').submit()">Update Deal</button>
            </div>
        </div>
    </div>

    <!-- Edit Task Modal -->
    <div class="modal-overlay" id="taskModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Edit Task</h3>
                <button class="close-btn" onclick="closeModal('taskModal')">✕</button>
            </div>
            <div class="modal-body">
                <form id="taskForm" method="POST">
                    <input type="hidden" name="task_id" id="edit_task_id">
                    <input type="hidden" name="update_task" value="1">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="edit_task_title">Title</label>
                            <input type="text" id="edit_task_title" name="task_title" required>
                        </div>

                        <div class="form-group full-width">
                            <label for="edit_task_description">Description</label>
                            <textarea id="edit_task_description" name="task_description" rows="4" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="edit_due_date">Due Date</label>
                            <input type="date" id="edit_due_date" name="due_date" required>
                        </div>

                        <div class="form-group">
                            <label for="edit_task_priority">Priority</label>
                            <select id="edit_task_priority" name="task_priority">
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_task_status">Status</label>
                            <select id="edit_task_status" name="task_status">
                                <option value="Pending">Pending</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_assigned_to">Assigned To</label>
                            <input type="text" id="edit_assigned_to" name="assigned_to" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('taskModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" onclick="document.getElementById('taskForm').submit()">Update Task</button>
            </div>
        </div>
    </div>

    <!-- Assign Task Modal -->
    <div class="modal-overlay" id="assignTaskModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Assign Task</h3>
                <button class="close-btn" onclick="closeModal('assignTaskModal')">✕</button>
            </div>
            <div class="modal-body">
                <form id="assignTaskForm" method="POST">
                    <input type="hidden" name="assign_task_id" id="assign_task_id">
                    <input type="hidden" name="assign_task" value="1">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="assign_to">Assign To</label>
                            <select id="assign_to" name="assign_to" required>
                                <option value="">Select Team Member</option>
                                <option value="John Doe">John Doe</option>
                                <option value="Jane Smith">Jane Smith</option>
                                <option value="Mike Johnson">Mike Johnson</option>
                                <option value="Sarah Wilson">Sarah Wilson</option>
                                <option value="David Brown">David Brown</option>
                                <option value="Emily Davis">Emily Davis</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('assignTaskModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" onclick="document.getElementById('assignTaskForm').submit()">Assign Task</button>
            </div>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div class="modal-overlay" id="addTaskModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Add New Task</h3>
                <button class="close-btn" onclick="closeModal('addTaskModal')">✕</button>
            </div>
            <div class="modal-body">
                <form id="addTaskForm" method="POST">
                    <input type="hidden" name="add_task" value="1">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="new_task_title">Title *</label>
                            <input type="text" id="new_task_title" name="new_task_title" required>
                        </div>

                        <div class="form-group">
                            <label for="new_task_description">Description *</label>
                            <textarea id="new_task_description" name="new_task_description" rows="4" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="new_due_date">Due Date *</label>
                            <input type="date" id="new_due_date" name="new_due_date" required>
                        </div>

                        <div class="form-group">
                            <label for="new_task_priority">Priority</label>
                            <select id="new_task_priority" name="new_task_priority">
                                <option value="Low">Low</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="High">High</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="new_assigned_to">Assigned To</label>
                            <select id="new_assigned_to" name="new_assigned_to">
                                <option value="">Unassigned</option>
                                <option value="John Doe">John Doe</option>
                                <option value="Jane Smith">Jane Smith</option>
                                <option value="Mike Johnson">Mike Johnson</option>
                                <option value="Sarah Wilson">Sarah Wilson</option>
                                <option value="David Brown">David Brown</option>
                                <option value="Emily Davis">Emily Davis</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="new_related_to">Related To</label>
                            <select id="new_related_to" name="new_related_to">
                                <option value="General" selected>General</option>
                                <option value="Lead">Lead</option>
                                <option value="Deal">Deal</option>
                                <option value="Customer">Customer</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="new_related_id">Related ID (Optional)</label>
                            <input type="number" id="new_related_id" name="new_related_id" min="0">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addTaskModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" onclick="document.getElementById('addTaskForm').submit()">Add Task</button>
            </div>
        </div>
    </div>

    <!-- View Lead Modal -->
    <div class="modal-overlay" id="viewModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Lead Details</h3>
                <button class="close-btn" onclick="closeModal('viewModal')">✕</button>
            </div>
            <div class="modal-body">
                <div id="leadDetails"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('viewModal')">Close</button>
            </div>
        </div>
    </div>

    <script>
        // Modal management system
        let currentOpenModal = null;

        function openModal(modalId) {
            // Close any currently open modal
            if (currentOpenModal) {
                closeModal(currentOpenModal);
            }
            
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
                currentOpenModal = modalId;
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('active');
                if (currentOpenModal === modalId) {
                    currentOpenModal = null;
                }
                document.body.style.overflow = ''; // Restore scrolling
            }
        }

        function closeAllModals() {
            const modals = document.querySelectorAll('.modal-overlay');
            modals.forEach(modal => {
                modal.classList.remove('active');
            });
            currentOpenModal = null;
            document.body.style.overflow = ''; // Restore scrolling
        }

        // Fetch data from database functions
        async function fetchLeadData(leadId) {
            try {
                const response = await fetch(`?action=get_lead_data&lead_id=${leadId}`);
                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Error fetching lead data:', error);
                return null;
            }
        }

        async function fetchDealData(dealId) {
            try {
                const response = await fetch(`?action=get_deal_data&deal_id=${dealId}`);
                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Error fetching deal data:', error);
                return null;
            }
        }

        async function fetchTaskData(taskId) {
            try {
                const response = await fetch(`?action=get_task_data&task_id=${taskId}`);
                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Error fetching task data:', error);
                return null;
            }
        }

        // Specific modal functions
        function assignTask(taskId) {
            document.getElementById('assign_task_id').value = taskId;
            openModal('assignTaskModal');
        }

        async function viewLead(leadId) {
            const details = document.getElementById('leadDetails');
            details.innerHTML = '<div class="loading-spinner"></div> Loading lead details...';
            openModal('viewModal');
            
            const leadData = await fetchLeadData(leadId);
            
            if (leadData) {
                details.innerHTML = `
                    <div class="lead-details">
                        <div class="detail-row">
                            <div class="detail-label">Lead ID:</div>
                            <div class="detail-value">#LEAD-${String(leadData.LeadID).padStart(3, '0')}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Name:</div>
                            <div class="detail-value">${leadData.FirstName} ${leadData.LastName}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Email:</div>
                            <div class="detail-value">${leadData.Email || 'N/A'}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Phone:</div>
                            <div class="detail-value">${leadData.Phone || 'N/A'}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Company:</div>
                            <div class="detail-value">${leadData.CompanyName || 'N/A'}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Job Title:</div>
                            <div class="detail-value">${leadData.JobTitle || 'N/A'}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Potential Value:</div>
                            <div class="detail-value">₱${parseFloat(leadData.PotentialValue).toFixed(2)}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Priority:</div>
                            <div class="detail-value">${leadData.Priority}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Status:</div>
                            <div class="detail-value">${leadData.Status}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Source:</div>
                            <div class="detail-value">${leadData.Source}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Notes:</div>
                            <div class="detail-value">${leadData.Notes || 'No notes available'}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Created:</div>
                            <div class="detail-value">${new Date(leadData.CreatedAt).toLocaleDateString()}</div>
                        </div>
                    </div>
                `;
            } else {
                details.innerHTML = '<div class="error-message">Failed to load lead details. Please try again.</div>';
            }
        }

        async function editLead(leadId) {
            const leadData = await fetchLeadData(leadId);
            
            if (leadData) {
                // Update the modal for editing
                document.getElementById('leadModalTitle').textContent = 'Edit Lead';
                document.getElementById('lead_id').value = leadData.LeadID;
                document.getElementById('add_lead').value = '';
                document.getElementById('first_name').value = leadData.FirstName;
                document.getElementById('last_name').value = leadData.LastName;
                document.getElementById('email').value = leadData.Email || '';
                document.getElementById('phone').value = leadData.Phone || '';
                document.getElementById('company').value = leadData.CompanyName || '';
                document.getElementById('job_title').value = leadData.JobTitle || '';
                document.getElementById('potential_value').value = leadData.PotentialValue;
                document.getElementById('priority').value = leadData.Priority;
                document.getElementById('status').value = leadData.Status;
                document.getElementById('source').value = leadData.Source;
                document.getElementById('notes').value = leadData.Notes || '';

                // Change the form action to update instead of add
                const form = document.getElementById('leadForm');
                // Remove any existing update_lead input
                const existingUpdateInput = form.querySelector('input[name="update_lead"]');
                if (existingUpdateInput) {
                    existingUpdateInput.remove();
                }
                
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'update_lead';
                hiddenInput.value = '1';
                form.appendChild(hiddenInput);

                openModal('addLeadModal');
            } else {
                alert('Failed to load lead data. Please try again.');
            }
        }

        async function editDeal(dealId) {
            const dealData = await fetchDealData(dealId);
            
            if (dealData) {
                document.getElementById('edit_deal_id').value = dealData.DealID;
                document.getElementById('edit_deal_name').value = dealData.DealName;
                document.getElementById('edit_deal_value').value = dealData.DealValue;
                document.getElementById('edit_stage').value = dealData.Stage;
                document.getElementById('edit_probability').value = dealData.Probability;
                document.getElementById('edit_close_date').value = dealData.CloseDate;
                document.getElementById('edit_deal_notes').value = dealData.Notes || '';

                openModal('dealModal');
            } else {
                alert('Failed to load deal data. Please try again.');
            }
        }

        async function editTask(taskId) {
            const taskData = await fetchTaskData(taskId);
            
            if (taskData) {
                document.getElementById('edit_task_id').value = taskData.TaskID;
                document.getElementById('edit_task_title').value = taskData.Title;
                document.getElementById('edit_task_description').value = taskData.Description || '';
                document.getElementById('edit_due_date').value = taskData.DueDate;
                document.getElementById('edit_task_priority').value = taskData.Priority;
                document.getElementById('edit_task_status').value = taskData.Status;
                document.getElementById('edit_assigned_to').value = taskData.AssignedTo || '';

                openModal('taskModal');
            } else {
                alert('Failed to load task data. Please try again.');
            }
        }

        // Close modals when clicking outside
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(this.id);
                }
            });
        });

        // Tab switching
        function switchTab(tabName) {
            window.location.href = '?tab=' + tabName;
        }

        // Dropdown functions
        function toggleFilter() {
            document.getElementById('filterContent').classList.toggle('show');
            document.getElementById('sortContent').classList.remove('show');
            document.getElementById('settingsContent').classList.remove('show');
        }

        function toggleSort() {
            document.getElementById('sortContent').classList.toggle('show');
            document.getElementById('filterContent').classList.remove('show');
            document.getElementById('settingsContent').classList.remove('show');
        }

        function toggleSettings() {
            document.getElementById('settingsContent').classList.toggle('show');
            document.getElementById('filterContent').classList.remove('show');
            document.getElementById('sortContent').classList.remove('show');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.matches('.icon-btn')) {
                document.getElementById('filterContent').classList.remove('show');
                document.getElementById('sortContent').classList.remove('show');
                document.getElementById('settingsContent').classList.remove('show');
            }
        });

        // Utility functions
        function refreshData() {
            location.reload();
        }

        function resetView() {
            window.location.href = '?tab=<?php echo $activeTab; ?>';
        }

        function exportData() {
            const tab = '<?php echo $activeTab; ?>';
            window.location.href = '?export=1&tab=' + tab;
        }

        // Delete functions
        function deleteLead(leadId) {
            if (confirm('Are you sure you want to delete this lead? This action cannot be undone.')) {
                window.location.href = '?delete=lead&id=' + leadId + '&tab=customers';
            }
        }

        function deleteTask(taskId) {
            if (confirm('Are you sure you want to delete this task? This action cannot be undone.')) {
                window.location.href = '?delete=task&id=' + taskId + '&tab=tasks';
            }
        }

        // Search functionality
        const searchBox = document.getElementById('globalSearch');
        searchBox.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.table tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Notification button
        document.querySelector('.notification-btn').addEventListener('click', function() {
            alert('5 new notifications');
        });

        // Auto-hide success message after 5 seconds
        setTimeout(() => {
            const successMessage = document.querySelector('.success-message');
            if (successMessage) {
                successMessage.style.display = 'none';
            }
        }, 5000);

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeAllModals();
            }
        });

        // Ensure all modals are closed on page load
        document.addEventListener('DOMContentLoaded', function() {
            closeAllModals();
        });
    </script>
</body>

</html>