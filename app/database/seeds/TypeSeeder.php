<?php

use Plat\Files;

class TypeSeeder extends Seeder
{
    public function run()
    {
        Plat\Files\FileType::create(['id' => 6, 'class' => Files\SurveyFile::class, 'description' => '調查檔案']);
        Plat\Files\FileType::create(['id' => 20, 'class' => Files\FolderComponent::class, 'description' => '資料夾']);
    }
}