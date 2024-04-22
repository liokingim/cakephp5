<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Articles Controller
 *
 * @property \App\Model\Table\ArticlesTable $Articles
 */
class ArticlesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->Authorization->skipAuthorization();

        $articles = $this->paginate($this->Articles);
        // $query = $this->Articles->find()
        //     ->contain(['Users']);
        // $articles = $this->paginate($query);

        $this->set(compact('articles'));
    }

    /**
     * View method
     *
     * @param string|null $id Article id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($slug = null)
    {
        $this->Authorization->skipAuthorization();

        // $article = $this->Articles->get($id, contain: ['Users', 'Tags']);
        $article = $this->Articles
            ->findBySlug($slug)
            ->contain('Tags')
            ->firstOrFail();
        $this->set(compact('article'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $article = $this->Articles->newEmptyEntity();
        $this->Authorization->authorize($article);

        if ($this->request->is('post')) {
            $article = $this->Articles->patchEntity($article, $this->request->getData());

            // Hardcoding the user_id is temporary, and will be removed later
            // when we build authentication out.
            $article->user_id = $this->request->getAttribute('identity')->getIdentifier();

            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to add your article.'));
        }

        $tags = $this->Article->Tags->find('list')->all();

        // $this->set('tags', $tags);
        // $this->set('article', $article);

        $this->set(compact('article', 'tags'));

        // $article = $this->Articles->newEmptyEntity();
        // if ($this->request->is('post')) {
        //     $article = $this->Articles->patchEntity($article, $this->request->getData());
        //     if ($this->Articles->save($article)) {
        //         $this->Flash->success(__('The article has been saved.'));

        //         return $this->redirect(['action' => 'index']);
        //     }
        //     $this->Flash->error(__('The article could not be saved. Please, try again.'));
        // }
        // $users = $this->Articles->Users->find('list', limit: 200)->all();
        // $tags = $this->Articles->Tags->find('list', limit: 200)->all();
        // $this->set(compact('article', 'users', 'tags'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Article id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($slug)
    {
        $article = $this->Articles
            ->findBySlug($slug)
            ->contain('Tags')
            ->firstOrFail();

        $this->Authorization->authorize($article);

        if ($this->request->is(['post', 'put'])) {
            $this->Articles->patchEntity($article, $this->request->getData(), [
                // Added: Disable modification of user_id.
                'accessibleFields' => ['user_id' => false]
            ]);

            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been updated.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to update your article.'));
        }

        // Get a list of tags.
        $tags = $this->Articles->Tags->find('list')->all();

        // Set tags to the view context
        // $this->set('tags', $tags);
        // $this->set('article', $article);
        $this->set(compact('article', 'tags'));

        // $article = $this->Articles->get($id, contain: ['Tags']);
        // if ($this->request->is(['patch', 'post', 'put'])) {
        //     $article = $this->Articles->patchEntity($article, $this->request->getData());
        //     if ($this->Articles->save($article)) {
        //         $this->Flash->success(__('The article has been saved.'));

        //         return $this->redirect(['action' => 'index']);
        //     }
        //     $this->Flash->error(__('The article could not be saved. Please, try again.'));
        // }
        // $users = $this->Articles->Users->find('list', limit: 200)->all();
        // $tags = $this->Articles->Tags->find('list', limit: 200)->all();
        // $this->set(compact('article', 'users', 'tags'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Article id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($slug)
    {
        $this->request->allowMethod(['post', 'delete']);

        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        $this->Authorization->authorize($article);

        if ($this->Articles->delete($article)) {
            $this->Flash->success(__('The {0} article has been deleted.', $article->title));
            return $this->redirect(['action' => 'index']);
        }

        // $this->request->allowMethod(['post', 'delete']);
        // $article = $this->Articles->get($id);
        // if ($this->Articles->delete($article)) {
        //     $this->Flash->success(__('The article has been deleted.'));
        // } else {
        //     $this->Flash->error(__('The article could not be deleted. Please, try again.'));
        // }

        // return $this->redirect(['action' => 'index']);
    }

    public function tags()
    {
        // The 'pass' key is provided by CakePHP and contains all
        // the passed URL path segments in the request.
        $tags = $this->request->getParam('pass');

        // Use the ArticlesTable to find tagged articles.
        $articles = $this->Articles->find('tagged', tags: $tags)
            ->all();

        // Pass variables into the view template context.
        $this->set([
            'articles' => $articles,
            'tags' => $tags
        ]);
    }
}
