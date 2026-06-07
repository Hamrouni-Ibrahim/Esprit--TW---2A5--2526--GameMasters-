<?php
class AvatarGenerator {
    
    public static function generateDefaultAvatar($username, $destinationDir) {
        $firstLetter = strtoupper(substr($username, 0, 1));
        $color = self::stringToColor($username);
        
        // Ensure destination exists
        if (!file_exists($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }
        
        $filename = 'default_' . uniqid() . '.svg';
        $fullPath = $destinationDir . $filename;
        
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
  <rect width="200" height="200" fill="$color"/>
  <text x="50%" y="50%" dy=".35em" text-anchor="middle" fill="#FFFFFF" font-family="Arial, sans-serif" font-size="100" font-weight="bold">$firstLetter</text>
</svg>
SVG;
        
        if (file_put_contents($fullPath, $svg)) {
            return $filename;
        }
        
        return false;
    }
    
    private static function stringToColor($string) {
        $hash = md5($string);
        return '#' . substr($hash, 0, 6);
    }
}
?>
