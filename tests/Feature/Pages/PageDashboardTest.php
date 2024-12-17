<?php

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('cannot be accessed by guest', function () {
    // Act & Assert
    get(route('dashboard'))
        ->assertRedirect(route('login'));
});

it('lists purchased courses', function () {
    // Arrange
    $user = User::factory()
                ->has(Course::factory()->count(2)->state(
                    new Sequence(
                        ['title' => 'Course A'],
                        ['title' => 'Course B'],
                    )))
                ->create();

    // Act & Assert
    $this->actingAs($user);
    get(route('dashboard'))
        ->assertOk()
        ->assertSeeText([
            'Course A',
            'Course B',
        ]);
});

it('does not list other courses', function () {
    // Arrange
    $user = User::factory()->create();
    $course = Course::factory()->create();

    // Act & Assert
    $this->actingAs($user);
    get(route('dashboard'))
        ->assertOk()
        ->assertDontSeeText($course->title);



});

it('show latest purchased courses first ', function () {
    //Arrange
    $user = User::factory()->create();
    $firstCourse = Course::factory()->create();
    $secondCourse = Course::factory()->create();

    $user->courses()->attach($firstCourse, ['created_at' => Carbon::yesterday()]);
    $user->courses()->attach($secondCourse, ['created_at' => Carbon::now()]);
    //Act
    $this->actingAs($user);
    get(route('dashboard'))
        ->assertOk()
        ->assertSeeTextInOrder([
            $secondCourse->title,
            $firstCourse->title
        ]);
});

it('includes link to product videos', function () {
    //Arrange
    $user = User::factory()
        ->has(Course::factory())
        ->create();
    //Act
    $this->actingAs($user);
    get(route('dashboard'))
        ->assertOk()
        ->assertSeeText('Watch videos')
        ->assertSee(route('page.course-videos', Course::first()));
});
