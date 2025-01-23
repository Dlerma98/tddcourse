<?php


use App\Mail\NewPurchasedMail;
use App\Models\Course;

it('includes purchase details', function () {
    // Arrange
$course = Course::factory()->create();
    // Act
    $mail = new NewPurchasedMail($course);

    // Assert
    $mail->assertSeeInText("Thanks for purchasing {$course->title}");
    $mail->assertSeeInText("Login");
    $mail->assertSeeInHtml(route("login"));
});
