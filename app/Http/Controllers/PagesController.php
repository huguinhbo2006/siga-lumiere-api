<?php

namespace App\Http\Controllers;

use App\Page;
use App\Altacurso;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class PagesController extends Controller
{

    public function mostrar(){
        return Page::select('id','name','slug')->get();
    }

    public function traer(Request $request){
        $pagina = Page::find($request['id']);
        return response()->json($pagina, 200);
    }

    public function contenido(Request $request){

        $page = Page::findOrFail($request->input('id'));

        $content = $request->input('content');

        $imagenesAntes = $this->extraerImagenes($page->content ?? []);

        $content = $this->procesarElementos($content,$page->id);

        $imagenesDespues = $this->extraerImagenes($content);

        $page->content = $content;
        $page->save();

        $this->limpiarImagenes($imagenesAntes,$imagenesDespues);

        return response()->json([
            'ok'=>true,
            'content'=>$page->content
        ]);
    }

    public function nuevo(Request $request){

        $validator = Validator::make($request->all(),[
            'nombre'=>'required|string|max:150',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        $page = Page::create([
            'name'=>$request->nombre,
            'slug'=>'/'.strtolower($request->nombre),
            'status'=>'draft',
            'content'=>[
                'version'=>1,
                'elements'=>[]
            ]
        ]);

        $this->crearCarpetaPagina($page->id);

        return response()->json($page,201);
    }

    public function cursos(){

        $hoy = Carbon::today();

        return Altacurso::query()
            ->join('calendarios','altacursos.idCalendario','=','calendarios.id')
            ->join('cursos','altacursos.idCurso','=','cursos.id')
            ->join('modalidades','altacursos.idModalidad','=','modalidades.id')
            ->where('calendarios.activo',1)
            ->where('calendarios.eliminado',0)
            ->whereDate('calendarios.inicio','<=',$hoy)
            ->whereDate('calendarios.fin','>=',$hoy)
            ->where('altacursos.activo',1)
            ->where('altacursos.eliminado',0)
            ->select([
                'altacursos.id',
                'altacursos.precio',
                'altacursos.inicio',
                'altacursos.fin',
                'altacursos.limitePago',
                'cursos.nombre as curso_nombre',
                'cursos.icono as curso_imagen',
                'modalidades.nombre as modalidad_nombre'
            ])
            ->get();
    }

    /* ============================= */

    private function crearCarpetaPagina($pageId){

        $ruta = base_path('pages/'.$pageId);

        if(!file_exists($ruta)){
            mkdir($ruta,0777,true);
        }
    }

    private function procesarElementos($content,$pageId){

        if(!isset($content['elements'])) return $content;

        foreach($content['elements'] as $i=>$element){

            $tipo = $element['type'];

            $content['elements'][$i]['props'] =
                $this->procesarImagenes($element['props'],$tipo,$pageId);

        }

        return $content;
    }

    private function procesarImagenes($data,$tipo,$pageId){

        if(is_array($data)){

            foreach($data as $key=>$value){

                if(is_string($value) && str_starts_with($value,'data:image')){

                    $data[$key] = $this->guardarImagen($value,$tipo,$pageId);

                }
                elseif(is_array($value)){

                    $data[$key] = $this->procesarImagenes($value,$tipo,$pageId);

                }

            }

        }

        return $data;
    }

    /* ============================= */

    private function guardarImagen($base64,$tipo,$pageId){

        preg_match('/data:image\/(\w+);base64,/',$base64,$type);

        $base64 = preg_replace('/^data:image\/\w+;base64,/','',$base64);
        $imagen = base64_decode($base64);

        $hash = md5($imagen);

        $carpeta = base_path("pages/$pageId/$tipo");

        if(!file_exists($carpeta)){
            mkdir($carpeta,0777,true);
        }

        $archivo = "$carpeta/$hash.jpg";

        if(!file_exists($archivo)){
            file_put_contents($archivo,$imagen);
        }

        return url("/pages/$pageId/$tipo/$hash.jpg");

    }



    /* ============================= */

    private function extraerImagenes($data){

        $imagenes=[];

        if(is_array($data)){

            foreach($data as $value){

                if(is_string($value) && str_contains($value,'/pages/')){
                    $imagenes[]=$value;
                }
                elseif(is_array($value)){
                    $imagenes=array_merge($imagenes,$this->extraerImagenes($value));
                }

            }

        }

        return $imagenes;
    }

    private function limpiarImagenes($antes,$despues){

        $eliminar=array_diff($antes,$despues);

        foreach($eliminar as $url){

            $ruta=str_replace(url('/'),'', $url);

            $archivo=base_path($ruta);

            if(file_exists($archivo)){
                unlink($archivo);
            }

        }

    }

}