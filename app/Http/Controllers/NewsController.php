<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware as ControllerMiddleware;

class NewsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new ControllerMiddleware('auth', except: ['index', 'show']),
            new ControllerMiddleware('admin', except: ['index', 'show']),
        ];
    }
    public function index(Request $request)
    {
        $q        = trim($request->input('q',''));
        $category = $request->string('category')->toString(); // slug
        $sort     = $request->input('sort','latest');         // latest|popular

        $query = News::query()
            ->with('category')
            ->whereNotNull('published_at')->where('published_at','<=',now())
            ->when($q, function($qq) use ($q){
                $qq->where(function($w) use ($q){
                    $w->where('title','like',"%{$q}%")
                      ->orWhere('excerpt','like',"%{$q}%")
                      ->orWhere('content','like',"%{$q}%");
                });
            })
            ->when($category, fn($qq)=>$qq->whereHas('category', fn($w)=>$w->where('slug',$category)));

        ($sort === 'popular')
            ? $query->orderByDesc('views')->orderByDesc('published_at')
            : $query->orderByDesc('is_pinned')->orderByDesc('published_at');

        $news = $query->paginate(6)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('news.index', compact('news','categories','q','category','sort'));
    }

    // 상세
    public function show(News $news)
    {
        $news->increment('views');
        return view('news.show', ['item'=>$news->load('category')]);
    }

    // 작성 폼
	public function create(Request $request)
	{
		// 드롭다운/라디오에 뿌릴 목록
		$categories = \App\Models\Category::orderBy('name')->get();

		// 기본값
		$item = new \App\Models\News([
			'published_at' => now('Asia/Seoul')->format('Y-m-d\TH:i'),
		]);

		// ?category=lec 같은 슬러그로 들어오면 자동 선택
		if ($slug = $request->string('category')->toString()) {
			$prefillId = \App\Models\Category::where('slug', $slug)->value('id');
			if ($prefillId) $item->category_id = $prefillId;
		}

		return view('news.create', compact('categories','item'));
	}


    // 저장
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'  => ['nullable','exists:categories,id'],
            'title'        => ['required','string','min:2','max:180'],
            'slug'         => ['nullable','string','max:200','unique:news,slug'],
            'excerpt'      => ['nullable','string','max:300'],
            'content'      => ['required','string','min:10'],
            'cover'        => ['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],
            'source_url'   => ['nullable','url','max:255'],
            'is_pinned'    => ['sometimes','boolean'],
            'published_at' => ['nullable','date'],
        ]);

        // slug 자동 + 유니크 보정
        $slug = $data['slug'] ?? Str::slug(Str::limit($data['title'], 60, ''));
        $data['slug'] = $this->uniqueSlug($slug);

        // excerpt 자동
        if (empty($data['excerpt'])) {
            $data['excerpt'] = Str::limit(strip_tags($data['content']), 300);
        }

        // 표지 업로드
        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('news','public');
        }

        // KST로 저장
        if (!empty($data['published_at'])) {
            $data['published_at'] = Carbon::parse($data['published_at'], 'Asia/Seoul');
        }

        $data['is_pinned'] = (bool)($data['is_pinned'] ?? false);

        $row = News::create($data);

        return redirect()->route('news.show', $row)->with('success','등록 완료');
    }

    // 수정 폼
    public function edit(News $news)
    {
        $categories = Category::orderBy('name')->get();
        $news->published_at = optional($news->published_at)->timezone('Asia/Seoul')?->format('Y-m-d\TH:i');
        return view('news.edit', ['item'=>$news, 'categories'=>$categories]);
    }

    // 수정 저장
    public function update(Request $request, News $news)
    {
        $data = $request->validate([
            'category_id'  => ['nullable','exists:categories,id'],
            'title'        => ['required','string','min:2','max:180'],
            'slug'         => ['nullable','string','max:200',"unique:news,slug,{$news->id}"],
            'excerpt'      => ['nullable','string','max:300'],
            'content'      => ['required','string','min:10'],
            'cover'        => ['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],
            'source_url'   => ['nullable','url','max:255'],
            'is_pinned'    => ['sometimes','boolean'],
            'published_at' => ['nullable','date'],
        ]);

        // slug 자동 + 유니크 보정
        $slug = $data['slug'] ?? Str::slug(Str::limit($data['title'], 60, ''));
        $data['slug'] = $this->uniqueSlug($slug, $news->id);

        if (empty($data['excerpt'])) {
            $data['excerpt'] = Str::limit(strip_tags($data['content']), 300);
        }

        if ($request->hasFile('cover')) {
            if ($news->cover_path) Storage::disk('public')->delete($news->cover_path);
            $data['cover_path'] = $request->file('cover')->store('news','public');
        }

        if (!empty($data['published_at'])) {
            $data['published_at'] = Carbon::parse($data['published_at'], 'Asia/Seoul');
        }

        $data['is_pinned'] = (bool)($data['is_pinned'] ?? false);

        $news->update($data);

        return redirect()->route('news.show',$news)->with('success','수정 완료');
    }

    // 삭제
    public function destroy(News $news)
    {
        if ($news->cover_path) Storage::disk('public')->delete($news->cover_path);
        $news->delete();
        return redirect()->route('news.index')->with('success','삭제 완료');
    }

    // 슬러그 유니크 보정
    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug ?: 'post';
        $try  = $base; $i = 2;

        while (
            News::where('slug', $try)
                ->when($ignoreId, fn($q)=>$q->where('id','!=',$ignoreId))
                ->exists()
        ) {
            $try = $base.'-'.$i;
            $i++;
        }
        return $try;
    }
}
