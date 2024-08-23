<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function list($type)
    {
        $categories = Category::where('type', $type)->get();
        if ($type == 'user') {
            return view('category.user', compact('categories'));
        }
        if ($type == 'post') {
            return view('category.post', compact('categories'));
        }
        if ($type == 'ticket') {
            return view('category.ticket', compact('categories'));
        }
        if ($type == 'interest') {
            return view('category.interest', compact('categories'));
        }
        if ($type == 'forum') {
            return view('category.forum', compact('categories'));
        }
        if ($type == 'news') {
            return view('category.news', compact('categories'));
        }
        if ($type == 'event') {
            return view('category.event', compact('categories'));
        }
        if ($type == 'report') {
            return view('category.report', compact('categories'));
        }
        if ($type == 'position') {
            return view('category.position', compact('categories'));
        }
        if ($type == 'individual-profession') {
            return view('category.profession', compact('categories'));
        }
        if ($type == 'hospital-specialization') {
            return view('category.hospital-specialization', compact('categories'));
        }
        if ($type == 'doctor-specialization') {
            return view('category.doctor-specialization', compact('categories'));
        }
        if ($type == 'elderly-specialization') {
            return view('category.elderly-specialization', compact('categories'));
        }
        if ($type == 'rehabilitation-specialization') {
            return view('category.rehabilitation-specialization', compact('categories'));
        }
        if ($type == 'association-sector') {
            return view('category.association-sector', compact('categories'));
        }
        if ($type == 'society-sector') {
            return view('category.society-sector', compact('categories'));
        }
        if ($type == 'company-sector') {
            return view('category.company-sector', compact('categories'));
        }
        if ($type == 'start-sector') {
            return view('category.start-sector', compact('categories'));
        }
    }
    public function add(Request $request)
    {
        $category = new Category();
        $category->name = $request->name;
        $category->type = $request->type;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            // $path = Storage::disk('s3')->putFile('user/' . $request->user_id . '/profile', $file);
            // $path = Storage::disk('s3')->url($path);
            $extension = $file->getClientOriginalExtension();
            $mime = explode('/', $file->getClientMimeType());
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/admin/categories/', $filename))
                $path =  '/uploads/admin/categories/' . $filename;
            $category->image = $path;
        }
        $category->save();
        return redirect()->back();
    }

    public function edit(Request $request, $id)
    {
        $category = Category::find($id);
        // $image = public_path($category->image);
        // if(file_exists($image)){
        //     unlink($image);
        // }

        $category->name = $request->name;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            // $path = Storage::disk('s3')->putFile('user/' . $request->user_id . '/profile', $file);
            // $path = Storage::disk('s3')->url($path);
            $extension = $file->getClientOriginalExtension();
            $mime = explode('/', $file->getClientMimeType());
            $filename = time() . '-' . uniqid() . '.' . $extension;
            if ($file->move('uploads/admin/categories/', $filename))
                $path =  '/uploads/admin/categories/' . $filename;
            $category->image = $path;
        }
        $category->save();
        return redirect()->back();
    }

    public function delete($id)
    {
        $category = Category::find($id);
        $category->delete();
        return redirect()->back();
    }

    public function subList($type, $id)
    {
        $categories = Category::where('type', $type)->where('parent_id', $id)->get();
        if ($type == 'individual-specialization') {
            $category = Category::find($id);
            return view('category.individual-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'individual-sub-specialization') {
            $category = Category::find($id);
            return view('category.individual-sub-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'hospital-sub-specialization') {
            $category = Category::find($id);
            return view('category.hospital-sub-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'doctor-sub-specialization') {
            $category = Category::find($id);
            return view('category.doctor-sub-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'elderly-sub-specialization') {
            $category = Category::find($id);
            return view('category.elderly-sub-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'rehabilitation-sub-specialization') {
            $category = Category::find($id);
            return view('category.rehabilitation-sub-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'association-sub-specialization') {
            $category = Category::find($id);
            return view('category.association-sub-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'association-specialization') {
            $category = Category::find($id);
            return view('category.association-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'society-specialization') {
            $category = Category::find($id);
            return view('category.society-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'society-sub-specialization') {
            $category = Category::find($id);
            return view('category.society-sub-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'company-specialization') {
            $category = Category::find($id);
            return view('category.company-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'company-sub-specialization') {
            $category = Category::find($id);
            return view('category.company-sub-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'start-specialization') {
            $category = Category::find($id);
            return view('category.start-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'start-sub-specialization') {
            $category = Category::find($id);
            return view('category.start-sub-specialization', compact('categories', 'type', 'id', 'category'));
        }
    }
    public function subCreate(Request $request)
    {
        $create = new Category();
        $create->name = $request->name;
        $create->type = $request->type;
        $create->parent_id = $request->parent_id;
        $create->save();
        return redirect()->back();
    }
}
