<?php

namespace App\Domains\LifeAreas\Http\Controllers;

use App\Shared\Http\Controllers\Controller;
use App\Domains\LifeAreas\Http\Requests\StoreLifeAreaRequest;
use App\Domains\LifeAreas\Http\Requests\UpdateLifeAreaRequest;
use App\Domains\LifeAreas\Models\LifeArea;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LifeAreaController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->to(route('settings.index').'#life-areas');
    }

    public function create()
    {
        return redirect()->to(route('settings.index').'#life-areas');
    }

    public function store(StoreLifeAreaRequest $request)
    {
        $maxSort = $request->user()->lifeAreas()->max('sort_order') ?? 0;
        $slug = $this->uniqueSlug($request->user()->id, $request->validated('name'));

        $request->user()->lifeAreas()->create([
            'name' => $request->validated('name'),
            'slug' => $slug,
            'color' => $request->validated('color'),
            'sort_order' => $maxSort + 1,
        ]);

        return redirect()->to(route('settings.index').'#life-areas')->with('success', 'Life area created.');
    }

    public function show(Request $request, LifeArea $lifeArea)
    {
        abort_unless($lifeArea->user_id === $request->user()->id, 403);
        $lifeArea->loadCount(['goals', 'tasks', 'habits']);

        return view('life-areas.show', compact('lifeArea'));
    }

    public function edit(Request $request, LifeArea $lifeArea)
    {
        abort_unless($lifeArea->user_id === $request->user()->id, 403);

        return redirect()->to(route('settings.index').'#life-areas');
    }

    public function update(UpdateLifeAreaRequest $request, LifeArea $lifeArea)
    {
        abort_unless($lifeArea->user_id === $request->user()->id, 403);

        $lifeArea->update([
            'name' => $request->validated('name'),
            'color' => $request->validated('color'),
            'slug' => $this->uniqueSlug($lifeArea->user_id, $request->validated('name'), $lifeArea->id),
        ]);

        return redirect()->to(route('settings.index').'#life-areas')->with('success', 'Life area updated.');
    }

    public function destroy(Request $request, LifeArea $lifeArea)
    {
        abort_unless($lifeArea->user_id === $request->user()->id, 403);

        if ($request->user()->lifeAreas()->count() <= 1) {
            return back()->with('error', 'You must keep at least one life area.');
        }

        $lifeArea->delete();

        return redirect()->to(route('settings.index').'#life-areas')->with('success', 'Life area deleted.');
    }

    private function uniqueSlug(int $userId, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'area';
        $slug = $base;
        $i = 1;

        while (LifeArea::where('user_id', $userId)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
