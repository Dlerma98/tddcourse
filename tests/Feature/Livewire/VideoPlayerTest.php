<?php


use App\Http\Livewire\VideoPlayer;
use App\Models\Course;
use App\Models\Video;
use Livewire\Livewire;

it('shows details for given video', function () {
    // Arrange
    $course = Course::factory()
        ->has(Video::factory())
        ->create();

    // Act & Assert
    $video = $course->videos()->first();
    Livewire::test(VideoPlayer::class, ['video' => $course->videos->first()])
        ->assertSee([
            $video->title,
            $video->description,
            "({$video->duration_in_min}min)",
        ]);
});


it('shows given video', function () {
    // Arrange
    $course = Course::factory()
        ->has(Video::factory())
        ->create();
    // Act & Assert
    $video = $course->videos()->first();
    Livewire::test(VideoPlayer::class, ['video' => $video])
        ->assertSeeHtml('<iframe src="https://player.vimeo.com/video/' . $video->vimeo_id .'"');
});
