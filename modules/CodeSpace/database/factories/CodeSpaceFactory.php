<?php

namespace Modules\CodeSpace\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\CodeSpace\Models\CodeSpace;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\CodeSpace\Models\CodeSpace>
 */
class CodeSpaceFactory extends Factory
{
    protected $model = CodeSpace::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(2, true);

        return [
            'name'    => ucfirst($name),
            'slug'    => Str::uuid(),
            'user_id' => User::factory(),
            'files'   => $this->defaultFiles(),
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    protected function defaultFiles(): array
    {
        return [
            'index.html' => [
                'name'     => 'index.html',
                'language' => 'html',
                'content'  => <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CodeSpace</title>
  <link rel="stylesheet" href="src/styles.css">
</head>
<body>
  <div id="app"></div>
  <script type="module" src="src/main.js"></script>
</body>
</html>
HTML
            ],
            'src/styles.css' => [
                'name'     => 'styles.css',
                'language' => 'css',
                'content'  => <<<'CSS'
body {
  font-family: system-ui, sans-serif;
  margin: 0;
  padding: 1rem;
}
CSS
            ],
            'src/main.js' => [
                'name'     => 'main.js',
                'language' => 'javascript',
                'content'  => <<<'JS'
const app = document.getElementById('app');

app.innerHTML = '<h1>Hello CodeSpace 👋</h1>';
JS
            ],
        ];
    }
}
