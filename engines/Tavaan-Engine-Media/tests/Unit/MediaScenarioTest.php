<?php

declare(strict_types=1);

namespace Tavaan\Engine\Media\Tests\Unit;

use Tavaan\Testing\TestCase;
use Tavaan\Engine\Media\Image;
use Tavaan\Engine\Media\MediaManager;

/**
 * T-NN scenario tests for the media engine.
 *
 * Generated from ROADMAP.md scenarios — real implementations, not placeholders.
 */
final class MediaScenarioTest extends TestCase
{
    public function test_t01_image_make_blank_canvas(): void
    {
        // Scenario T-01
if (!function_exists("imagecreatetruecolor")) {
            return; // GD not available — skip
        }
        $img = new Image();
        $img->make(null, 50, 30);
        $this->assertSame(50, $img->getWidth());
        $this->assertSame(30, $img->getHeight());
    }
    public function test_t02_image_resize_changes_dimensions(): void
    {
        // Scenario T-02
if (!function_exists("imagecreatetruecolor")) {
            return; // GD not available — skip
        }
        $img = new Image();
        $img->make(null, 100, 100);
        $img->resize(50, 50, false);
        $this->assertSame(50, $img->getWidth());
        $this->assertSame(50, $img->getHeight());
    }
    public function test_t03_media_manager_frompath_missing_returns_null(): void
    {
        // Scenario T-03
$mgr = new MediaManager(["upload_dir" => "/tmp/test_media"]);
        $this->assertNull($mgr->fromPath("/nonexistent/file"));
    }
}
