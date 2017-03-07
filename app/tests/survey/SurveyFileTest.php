<?php

use Plat\Files\SurveyFile;
use Plat\Eloquent\Survey as SurveyORM;

class NodeTest extends TestCase {

    public function setUp()
    {
        parent::setUp();
        $this->app->make('artisan')->call('migrate');
        $this->file = Files::create(['id' => 1, 'type' => 6, 'title' => '', 'created_by' => 1]);
        $book = SurveyORM\Book::create(['file_id' => $this->file->id, 'title' => '', 'lock' => false]);        
        $this->user = new User;
        $this->user->username = 'gg';
        $this->user->email    = 'tim72117@gmail.com';
        $this->user->actived = false;
        $this->user->disabled = false;
        $this->user->save();   
    }

    public function testSurveyFile()
    {    
        return new SurveyFile($this->file, $this->user);
    }

    /**
     * @depends testSurveyFile
     */
    public function testGetNodes($surveyFile)
    {
        Input::replace([
            'root' => $surveyFile->file->book->toArray(),
        ]);

        $nodes = $surveyFile->getNodes()['nodes'];

        $this->assertInstanceOf(Illuminate\Database\Eloquent\Collection::class, $nodes);

        return $nodes;
    }

    /**
     * @depends testSurveyFile
     * @depends testGetNodes
     */
	public function testCreateNode($surveyFile, $nodes)
	{
        Input::replace([
            'parent' => $surveyFile->file->book->toArray(),
            'node' => ['type' => 'select'],
            'previous' => $nodes->first()->toArray(),
        ]);

        $node = $surveyFile->createNode()['node'];

        $this->assertInstanceOf(SurveyORM\Node::class, $node);

        $this->assertCount($nodes->count()+1, $surveyFile->getNodes()['nodes']);

        //ddd(SurveyORM\Node::find($node->id)->questions);

        //$this->assertCount(1, $node->questions);

        return $node;
    }

    /**
     * @depends testSurveyFile
     * @depends testCreateNode
     */
    public function testSaveNodeTitle($surveyFile, $node)
    {
        $title = (string)rand(5, 15);
        Input::replace([
            'node' => [
                'id' => $node->id,
                'title' => $title,
            ],
        ]);

        $surveyFile->saveNodeTitle()['title'];

        $this->assertSame($title, SurveyORM\Node::find($node->id)->title);
    }

    /**
     * @depends testSurveyFile
     * @depends testCreateNode
     */
    public function testCreateAnswer($surveyFile, $node)
    {
        Input::replace([
            'node' => $node->toArray(),
            'previous' => $node->answers->first() ? $node->answers->first()->toArray() : NULL,
        ]);

        $answer = $surveyFile->createAnswer()['answer'];

        $this->assertInstanceOf(SurveyORM\Answer::class, $answer);

        return $answer;
    }

    /**
     * @depends testSurveyFile
     * @depends testCreateAnswer
     */
    public function testGetNodesInAnswer($surveyFile, $answer)
    {
        Input::replace([
            'parent' => $answer->toArray(),
        ]);

        $nodes = $surveyFile->getNodes()['nodes'];

        $this->assertInstanceOf(Illuminate\Database\Eloquent\Collection::class, $nodes);

        return $nodes;
    }

    /**
     * @depends testSurveyFile
     * @depends testGetNodesInAnswer
     * @depends testCreateAnswer
     */
    public function testCreateNodeInAnswer($surveyFile, $nodes, $answer)
    {
        Input::replace([
            'parent' => $answer->toArray(),
            'node' => ['type' => 'select'],
            'previous' => $nodes->first()->toArray(),
        ]);

        $node = $surveyFile->createNode()['node'];

        $this->assertInstanceOf(SurveyORM\Node::class, $node);

        $this->assertCount($nodes->count()+1, $surveyFile->getNodes()['nodes']);

        return $node;
    }

    /**
     * @depends testSurveyFile
     * @depends testCreateNode
     */
    public function testRemoveNode($surveyFile, $node)
    {
        Input::replace([
            'parent' => $surveyFile->file->book->toArray(),
            'node' => $node->toArray(),
        ]);

        $nodes = $surveyFile->getNodes()['nodes'];

        $questions_id = $node->questions->fetch('id');

        $surveyFile->removeNode();

        $this->assertCount($nodes->count()-1, $surveyFile->getNodes()['nodes']);

    }

}