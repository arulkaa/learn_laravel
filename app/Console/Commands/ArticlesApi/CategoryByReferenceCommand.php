<?php
/**
 * @copyright C VR Solutions 2018
 *
 * This software is the property of VR Solutions
 * and is protected by copyright law – it is NOT freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * Contact VR Solutions:
 * E-mail: vytautas.rimeikis@gmail.com
 * http://www.vrwebdeveloper.lt
 */

declare(strict_types = 1);

namespace App\Console\Commands\ArticlesApi;

use App\Category;
use GuzzleHttp\Client;

/**
 * Class CategoryByReferenceCommand
 * @package App\Console\Commands\ArticlesApi
 */
class CategoryByReferenceCommand extends ArticleBase
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:category-by-id {reference_category_id : Category ID on 3trd party application}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get category by id';

    /**
     * CategoryByReferenceCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            $client = new Client();

            $response = $client->get($this->getCallUrl());

            $data = json_decode($response->getBody()->getContents());

            if (!$data->success) {
                $this->error($data->message);
                exit();
            }

            $category = Category::updateOrCreate(
                ['slug' => $data->data->slug],
                ['title' => $data->data->title, 'reference_category_id' => $data->data->category_id]
            );

            $this->info('Category ' . $category->title . ' updated or created successfully.');
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * @return string
     */
    protected function getCallUrl(): string
    {
        return strtr(':url/category/:id', [
            ':url' => parent::getCallUrl(),
            ':id' => $this->argument('reference_category_id'),
        ]);
    }
}
