<?php
require_once 'config.php';
header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        try {
            $stmt = $pdo->query("SELECT * FROM courses ORDER BY id ASC");
            $courses = $stmt->fetchAll();
            echo json_encode(['success' => true, 'courses' => $courses]);
        } catch (PDOException $e) {
            // Fallback to hardcoded courses if table doesn't exist yet
            $courses = [
                ['id'=>1,'title'=>'Full Stack Web Development','desc'=>'Master HTML, CSS, JS, React, Node.js and build dynamic web apps.','level'=>'Intermediate','rating'=>'4.8','time'=>'8h 30m','cat'=>'development','instructor'=>'John Parker','img'=>'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=400&h=200&fit=crop','price'=>99,'enrolled'=>1250,'progress'=>65],
                ['id'=>2,'title'=>'Machine Learning Basics','desc'=>'Understand algorithms, train models, and deploy ML apps with Python.','level'=>'Advanced','rating'=>'4.9','time'=>'10h','cat'=>'ai','instructor'=>'Dr. Aisha Khan','img'=>'https://images.unsplash.com/photo-1581090700227-1e37b190418e?w=400&h=200&fit=crop','price'=>149,'enrolled'=>890,'progress'=>0],
                ['id'=>3,'title'=>'UI/UX Design for Beginners','desc'=>'Learn Figma, typography, wireframing, and design psychology.','level'=>'Beginner','rating'=>'4.6','time'=>'6h 45m','cat'=>'design','instructor'=>'Elena Rose','img'=>'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=400&h=200&fit=crop','price'=>79,'enrolled'=>2100,'progress'=>100],
                ['id'=>4,'title'=>'Entrepreneurship Essentials','desc'=>'Learn how to start and scale your business with proven models.','level'=>'Intermediate','rating'=>'4.7','time'=>'5h 20m','cat'=>'business','instructor'=>'Michael Stone','img'=>'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?w=400&h=200&fit=crop','price'=>89,'enrolled'=>567,'progress'=>30],
                ['id'=>5,'title'=>'Data Science with Python','desc'=>'Dive into data visualization, cleaning, and statistical analysis.','level'=>'Advanced','rating'=>'4.9','time'=>'12h 15m','cat'=>'ai','instructor'=>'Dr. Lin Wei','img'=>'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=400&h=200&fit=crop','price'=>159,'enrolled'=>1890,'progress'=>0],
                ['id'=>6,'title'=>'Digital Marketing Strategy','desc'=>'Master SEO, SEM, and social media advertising for maximum reach.','level'=>'Beginner','rating'=>'4.5','time'=>'7h 0m','cat'=>'business','instructor'=>'Sarah Lee','img'=>'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400&h=200&fit=crop','price'=>69,'enrolled'=>3200,'progress'=>85]
            ];
            echo json_encode(['success' => true, 'courses' => $courses]);
        }
        break;

    case 'POST':
        requireAuth();
        $input = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $pdo->prepare("INSERT INTO courses (title, `desc`, level, rating, time, cat, instructor, img, price, enrolled, progress) VALUES (?,?,?,?,?,?,?,?,?,0,0)");
            $stmt->execute([
                $input['title'] ?? '',
                $input['desc'] ?? '',
                $input['level'] ?? 'Beginner',
                '5.0',
                $input['time'] ?? '1h',
                $input['cat'] ?? 'development',
                $_SESSION['username'] ?? 'Instructor',
                $input['img'] ?? 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=200&fit=crop',
                (int)($input['price'] ?? 0)
            ]);
            $newId = $pdo->lastInsertId();
            echo json_encode(['success' => true, 'course' => array_merge($input, ['id' => $newId, 'enrolled' => 0, 'rating' => '5.0'])]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to create course']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
