<?php

namespace App\Http\Controllers\Blog\Admin;

use Illuminate\Http\Request;
use App\Models\BlogCategory;
use App\Repositories\BlogCategoryRepository;
use Illuminate\Support\Str;
use App\Http\Requests\BlogCategoryUpdateRequest;
use App\Http\Requests\BlogCategoryCreateRequest;

class CategoryController extends BaseController
{
     /**
     * @var BlogCategoryRepository
     */
    private $blogCategoryRepository;

    public function __construct()
    {
       // parent::__construct();
        $this->blogCategoryRepository = app(BlogCategoryRepository::class);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$paginator = BlogCategory::paginate(5);
        $paginator = $this->blogCategoryRepository->getAllWithPaginate(5); BlogCategory::paginate(5);
        return view('blog.admin.categories.index', compact('paginator'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $item = new BlogCategory();
        $categoryList = $this->blogCategoryRepository->getForComboBox();

        return view('blog.admin.categories.edit', compact('item', 'categoryList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BlogCategoryCreateRequest $request)
    {
        $data = $request->input(); //отримаємо масив даних, які надійшли з форми

        /*if (empty($data['slug'])) { //якщо псевдонім порожній
            $data['slug'] = Str::slug($data['title']); //генеруємо псевдонім
        }*/

        $item = (new BlogCategory())->create($data); //створюємо об'єкт і додаємо в БД

        if ($item) {
            return redirect()
                ->route('blog.admin.categories.edit', [$item->id])
                ->with(['success' => 'Успішно збережено']);
        } else {
            return back()
                ->withErrors(['msg' => 'Помилка збереження'])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = $this->blogCategoryRepository->getEdit($id);
        if (empty($item)) {                         //помилка, якщо репозиторій не знайде наш ід
            abort(404);
        }
        $categoryList = $this->blogCategoryRepository->getForComboBox($item->parent_id);

        return view('blog.admin.categories.edit', compact('item', 'categoryList'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BlogCategoryUpdateRequest $request, $id)
    {
        $item = $this->blogCategoryRepository->getEdit($id);
        if (empty($item)) { //якщо ід не знайдено
            return back() //redirect back
                ->withErrors(['msg' => "Запис id=[{$id}] не знайдено"]) //видати помилку
                ->withInput(); //повернути дані
        }

        $data = $request->all(); //отримаємо масив даних, які надійшли з форми

        /*if (empty($data['slug'])) { //якщо псевдонім порожній
            $data['slug'] = Str::slug($data['title']); //генеруємо псевдонім
            //TODO: ВАЛІДАЦІЯ СЛАГА НА УНІКАЛЬНІСТЬ
        }*/

        $result = $item->update($data);  //оновлюємо дані об'єкта і зберігаємо в БД

        if ($result) {
            return redirect()
                ->route('blog.admin.categories.edit', $item->id)
                ->with(['success' => 'Успішно збережено']);
        } else {
            return back()
                ->with(['msg' => 'Помилка збереження'])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
