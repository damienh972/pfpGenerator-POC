<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Layer;
use Illuminate\Http\Request;
use App\Services\IPFSService;

class LayerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $layers = Layer::all();
        return response()->json($layers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'image_cids' => 'required|array',
        ]);

        $maxIndex = Layer::where('project_id', $validatedData['project_id'])->max('index');

        $newIndex = $maxIndex !== null ? $maxIndex + 1 : 0;

        $layer = new Layer;
        $layer->project_id = $validatedData['project_id'];
        $layer->name = $validatedData['name'];
        $layer->image_cids = $validatedData['image_cids'];
        $layer->index = $newIndex;

        $layer->save();

        return response()->json($layer, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $layer = Layer::find($id);
        if (!$layer) {
            return response()->json(['message' => 'Layer not found'], 404);
        }
        return response()->json($layer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $layer = Layer::findOrFail($id);
        $project_id = $layer->project_id;

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'image_cids' => 'sometimes|required|array',
            'index' => 'required|integer',
        ]);

    
        $currentIndex = $layer->index;
        $newIndex = $validatedData['index'];

        /**
         * Update the index of the layers that are between the current index and the new index
         */
        DB::transaction(function () use ($layer, $newIndex, $currentIndex, $project_id) {
           
            if ($newIndex > $currentIndex) {
                Layer::where('project_id', $project_id)
                    ->where('index', '>', $currentIndex)
                    ->where('index', '<=', $newIndex)
                    ->decrement('index');
            } elseif ($newIndex < $currentIndex) {
                Layer::where('project_id', $project_id)
                    ->where('index', '<', $currentIndex)
                    ->where('index', '>=', $newIndex)
                    ->increment('index');
            }
            $layer->update(['index' => $newIndex]);

        });
        if($validatedData['name']) {
            $layer->update(['name' => $validatedData['name']]);
        }

        return response()->json(['message' => 'Layer updated successfully', 'data' => $layer]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $layer = Layer::find($id);
        if (!$layer) {
            return response()->json(['message' => 'Layer not found'], 404);
        }

        $layer->delete();
        return response()->json(['message' => 'Layer deleted successfully']);
    }

    public function addImageToLayer(Request $request, $layerId, IPFSService $ipfsService)
    {
        $layer = Layer::findOrFail($layerId);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
           
            $cid = $ipfsService->pinFile($image);
            $imageName = $image->getClientOriginalName();

            $currentImages = $layer->image_cids ?? [];
            $currentImages[] = ['cid' => $cid, 'name' => $imageName];

            $layer->image_cids = $currentImages;
            $layer->save();

            return response()->json(['message' => 'Image added successfully', 'layer' => $layer]);
        } else {
            return response()->json(['message' => 'No image provided'], 400);
        }
    }

    public function removeImageFromLayer(Request $request, $layerId, $cid, IPFSService $ipfsService)
    {
        $layer = Layer::findOrFail($layerId);

        $ipfsService->unpinFile($cid);


        $currentImages = $layer->image_cids ?? [];
        $filteredImages = array_filter($currentImages, function ($image) use ($cid) {
            return isset($image['cid']['IpfsHash']) ? $image['cid']['IpfsHash'] !== $cid : true;
        });

        $layer->image_cids = $filteredImages; 
        $layer->save();

        return response()->json(['message' => 'Image removed successfully', 'layer' => $layer]);
    }

    public function updateImageInLayer(Request $request, $layerId, $cid, IPFSService $ipfsService)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
        ]);
        
        $layer = Layer::findOrFail($layerId);

        $ipfsService->updateMetadata($cid, ['name' => $validatedData['name']]);

        $currentImages = $layer->image_cids ?? [];
        $updatedImages = array_map(function ($image) use ($cid, $validatedData) {
            if (isset($image['cid']['IpfsHash']) && $image['cid']['IpfsHash'] === $cid) {
                $image['name'] = $validatedData['name'];
            }
            return $image;
        }, $currentImages);

        $layer->image_cids = $updatedImages;
        $layer->save();

        return response()->json(['message' => 'Image updated successfully', 'layer' => $layer]);
    }
}
