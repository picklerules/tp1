<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        $articles = Article::orderBy('created_at', 'desc')->get();
        
        return view('article.index', compact('articles', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('article.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titre_en' => 'required|max:255',
            'contenu_en' => 'required|max:1000',
            'titre_fr' => 'nullable|max:255',
            'contenu_fr' => 'nullable|max:1000',
        ]);

    
        // Vérifier si le titre et le contenu sont de la même langue
        $inconsistentLanguage = false;
        if (!empty($request->titre_en) || !empty($request->contenu_en)) {
            if (empty($request->titre_en) || empty($request->contenu_en)) {
                $inconsistentLanguage = true;
            }
        }
        if (!empty($request->titre_fr) || !empty($request->contenu_fr)) {
            if (empty($request->titre_fr) || empty($request->contenu_fr)) {
                $inconsistentLanguage = true;
            }
        }
    
        if ($inconsistentLanguage) {
            return back()->withErrors(__('lang.inconsistent_language'))->withInput();
        }


        $titre = ['en' => $request->titre_en];
        $contenu = ['en' => $request->contenu_en];

        if($request->contenu_fr != null) { 
            $contenu = $contenu + ['fr' => $request->contenu_fr];
        };

        if($request->titre_fr != null) { 
            $titre = $titre + ['fr' => $request->titre_fr];
        };
        
        Article::create([
            'titre' => $titre,
            'contenu' => $contenu,
            'user_id' => auth()->id()
        ]);

        return redirect()->route('article.index')->withSuccess(__('lang.article_created_success'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        if (auth()->id() !== $article->user_id) {
            return redirect()->route('article.index')->withErrors(__('lang.non_authorized_action'));
        }
    
        return view('article.edit', compact('article'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        if (auth()->id() !== $article->user_id) {
            return redirect()->route('article.index')->withErrors(__('lang.non_authorized_action'));
        }
    
        $request->validate([
            'titre_en' => 'required|max:255',
            'contenu_en' => 'required|max:1000',
            'titre_fr' => 'nullable|max:255',
            'contenu_fr' => 'nullable|max:1000',
        ]);

        // Vérifier si le titre et le contenu sont de la même langue
        $inconsistentLanguage = false;
        if (!empty($request->titre_en) || !empty($request->contenu_en)) {
            if (empty($request->titre_en) || empty($request->contenu_en)) {
                $inconsistentLanguage = true;
            }
        }
        if (!empty($request->titre_fr) || !empty($request->contenu_fr)) {
            if (empty($request->titre_fr) || empty($request->contenu_fr)) {
                $inconsistentLanguage = true;
            }
        }
    
        if ($inconsistentLanguage) {
            return back()->withErrors(__('lang.inconsistent_language'))->withInput();
        }

        $titre = ['en' => $request->titre_en];
        $contenu = ['en' => $request->contenu_en];

        if($request->contenu_fr != null) { 
            $contenu = $contenu + ['fr' => $request->contenu_fr];
        };

        if($request->titre_fr != null) { 
            $titre = $titre + ['fr' => $request->titre_fr];
        };
        $article->update([
            'titre' => $titre,
            'contenu' => $contenu,
        ]);
    
        return redirect()->route('article.index')->withSuccess(__('lang.article_updated_success'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        if (auth()->id() !== $article->user_id) {
            return redirect()->route('article.index')->withErrors(__('lang.non_authorized_action'));
        }
    
        $article->delete();
    
        return redirect()->route('article.index')->withSuccess(__('lang.article_deleted_success'));
    }
}
