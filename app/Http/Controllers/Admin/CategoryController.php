<?php

namespace App\Http\Controllers\Admin;

use App\Actions\FileUploadAction;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function list($type)
    {
        $categories = Category::where('type', $type)
            ->orderByRaw("CASE WHEN name = 'No Title' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->get();
        if ($type == 'user') {
            return view('general.category.user', compact('categories'));
        }
        if ($type == 'post') {
            return view('general.category.post', compact('categories'));
        }
        if ($type == 'ticket') {
            return view('general.category.ticket', compact('categories'));
        }
        if ($type == 'interest') {
            return view('general.category.interest', compact('categories'));
        }
        if ($type == 'forum') {
            return view('general.category.forum', compact('categories'));
        }
        if ($type == 'news') {
            return view('general.category.news', compact('categories'));
        }
        if ($type == 'event') {
            return view('general.category.event', compact('categories'));
        }
        if ($type == 'report') {
            return view('general.category.report', compact('categories'));
        }
        if ($type == 'department') {
            return view('general.category.department', compact('categories'));
        }
        if ($type == 'training') {
            return view('general.category.training', compact('categories'));
        }
        if ($type == 'doctor-training') {
            return view('general.category.doctor-training', compact('categories'));
        }
        if ($type == 'rehabilitation-training') {
            return view('general.category.rehabilitation-training', compact('categories'));
        }
        if ($type == 'elderly-care-training') {
            return view('general.category.elderly-care-training', compact('categories'));
        }
        if ($type == 'healthcare-profession') {
            return view('general.category.healthcare-profession', compact('categories'));
        }
        if ($type == 'stem-profession') {
            return view('general.category.stem-profession', compact('categories'));
        }
        if ($type == 'management-profession') {
            return view('general.category.management-profession', compact('categories'));
        }
        if ($type == 'hospital-department') {
            return view('general.category.hospital-department', compact('categories'));
        }
        if ($type == 'doctor-specialization') {
            return view('general.category.doctor-specialization', compact('categories'));
        }
        if ($type == 'elderly-care') {
            return view('general.category.elderly-care', compact('categories'));
        }
        if ($type == 'rehabilitation-specialization') {
            return view('general.category.rehabilitation-specialization', compact('categories'));
        }
        if ($type == 'association-sector') {
            return view('general.category.association-sector', compact('categories'));
        }
        if ($type == 'society-sector') {
            return view('general.category.society-sector', compact('categories'));
        }
        if ($type == 'company-sector') {
            return view('general.category.company-sector', compact('categories'));
        }
        if ($type == 'start-sector') {
            return view('general.category.start-sector', compact('categories'));
        }
        if ($type == 'individual-title') {
            return view('general.category.individual-title', compact('categories'));
        }
        if ($type == 'hospital-training-focus') {
            return view('general.category.hospital-training-focus', compact('categories'));
        }
        if ($type == 'hospital-training-qualification') {
            return view('general.category.hospital-training-qualification', compact('categories'));
        }
        if ($type == 'rehabilitation-training-focus') {
            return view('general.category.rehabilitation-training-focus', compact('categories'));
        }
        if ($type == 'rehabilitation-training-qualification') {
            return view('general.category.rehabilitation-training-qualification', compact('categories'));
        }
        if ($type == 'staff-benefit') {
            return view('general.category.staff-benefit', compact('categories'));
        }
        if ($type == 'facility-special-feature') {
            return view('general.category.facility-special-feature', compact('categories'));
        }
        if ($type == 'treatment-service') {
            return view('general.category.treatment-service', compact('categories'));
        }
        if ($type == 'organization-legal-type') {
            return view('general.category.organization-legal-type', compact('categories'));
        }
        if ($type == 'organization-yearly-revenue') {
            return view('general.category.organization-yearly-revenue', compact('categories'));
        }
        if ($type == 'start-finance-stage') {
            return view('general.category.start-finance-stage', compact('categories'));
        }
        if ($type == 'start-target-group') {
            return view('general.category.start-target-group', compact('categories'));
        }
        if ($type == 'start-medical-focus') {
            return view('general.category.start-medical-focus', compact('categories'));
        }
        if ($type == 'start-company-feature') {
            return view('general.category.start-company-feature', compact('categories'));
        }
        if ($type == 'organization-special-feature') {
            return view('general.category.organization-special-feature', compact('categories'));
        }
        if ($type == 'designation') {
            return view('general.category.designation', compact('categories'));
        }
    }
    public function add(Request $request)
    {
        $category = new Category();
        $category->name = $request->name;
        $category->type = $request->type;
        if ($request->has('status')) {
            $category->status = $request->status;
        }
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = FileUploadAction::handle('admin/categories', $file);
            $category->image = $path;
        }
        $category->save();
        return redirect()->back();
    }

    public function edit(Request $request, $id)
    {
        $category = Category::find($id);
        Storage::disk('s3')->delete($category->image);
        $category->name = $request->name;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = FileUploadAction::handle('admin/categories', $file);
            $category->image = $path;
        }
        if ($request->has('status')) {
            $category->status = $request->status;
        }
        $category->save();
        return redirect()->back();
    }

    public function delete($id)
    {
        $category = Category::find($id);
        Storage::disk('s3')->delete($category->image);
        $category->delete();
        return redirect()->back();
    }

    public function subList($type, $id)
    {
        $categories = Category::where('type', $type)->where('parent_id', $id)->get();
        if ($type == 'healthcare-specialization') {
            $category = Category::find($id);
            return view('general.category.healthcare-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'healthcare-sub-specialization') {
            $category = Category::find($id);
            return view('general.category.healthcare-sub-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'stem-specialization') {
            $category = Category::find($id);
            return view('general.category.stem-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'stem-sub-specialization') {
            $category = Category::find($id);
            return view('general.category.stem-sub-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'management-specialization') {
            $category = Category::find($id);
            return view('general.category.management-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'management-sub-specialization') {
            $category = Category::find($id);
            return view('general.category.management-sub-specialization', compact('categories', 'type', 'id', 'category'));
        }

        if ($type == 'hospital-specialization') {
            $category = Category::find($id);
            return view('general.category.hospital-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'hospital-sub-specialization') {
            $category = Category::find($id);
            return view('general.category.hospital-sub-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'doctor-sub-specialization') {
            $category = Category::find($id);
            return view('general.category.doctor-sub-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'elderly-specialization') {
            $category = Category::find($id);
            return view('general.category.elderly-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'elderly-sub-specialization') {
            $category = Category::find($id);
            return view('general.category.elderly-sub-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'rehabilitation-sub-specialization') {
            $category = Category::find($id);
            return view('general.category.rehabilitation-sub-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'association-sub-specialization') {
            $category = Category::find($id);
            return view('general.category.association-sub-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'association-specialization') {
            $category = Category::find($id);
            return view('general.category.association-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'society-specialization') {
            $category = Category::find($id);
            return view('general.category.society-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'society-sub-specialization') {
            $category = Category::find($id);
            return view('general.category.society-sub-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'company-specialization') {
            $category = Category::find($id);
            return view('general.category.company-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'company-sub-specialization') {
            $category = Category::find($id);
            return view('general.category.company-sub-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'start-specialization') {
            $category = Category::find($id);
            return view('general.category.start-specialization', compact('categories', 'type', 'id', 'category'));
        }
        if ($type == 'start-sub-specialization') {
            $category = Category::find($id);
            return view('general.category.start-sub-specialization', compact('categories', 'type', 'id', 'category'));
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


    public function addCsv(Request $request, $type)
    {
        $file = $request->file('file');

        if (!$file) {
            return response()->json(['message' => 'Please upload a CSV file.'], 400);
        }

        // Parse the CSV file with semicolon as the delimiter
        $csvData = [];
        if (($handle = fopen($file->getPathname(), 'r')) !== false) {
            // Get the header row and include its first item
            $headerRow = fgetcsv($handle, 1000, ';');
            if ($headerRow && !empty($headerRow[0])) {
                $csvData[] = $headerRow[0];
            }

            // Get each row of the CSV and collect only the values
            while (($row = fgetcsv($handle, 1000, ';')) !== false) {
                foreach ($row as $value) {
                    if (!empty($value)) {
                        $csvData[] = $value;
                    }
                }
            }
            fclose($handle);
        }
        foreach ($csvData as $item) {
            $create = new Category();
            $create->name = $item;
            $create->type = $type;
            $create->type = $type;
            $create->parent_id = $request->parent_id;
            $create->save();
        }


        return redirect()->back();
        return response()->json([
            'status' => true,
            'action' => 'File ',
            'data' => $csvData,
        ]);
        return response()->json([
            'status' => 0,
            'message' => 'Invalid type provided.',
        ]);
    }
}
