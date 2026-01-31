# 📚 Referencia Completa: Laravel (News-Blog-App)

> **Propósito**: Guía de referencia para crear nuevas aplicaciones Laravel siguiendo exactamente la misma estructura y sintaxis. **NO inventar funciones ni métodos que no estén aquí.**

---

## 📑 ÍNDICE

1. [Consideraciones Obligatorias](#1️⃣-consideraciones-obligatorias)
2. [Estructura de Carpetas](#2️⃣-estructura-de-carpetas)
3. [Try...Catch en BD](#3️⃣-trycatch-en-operaciones-bd)
4. [Validación de Entradas](#4️⃣-validación-de-entradas)
5. [Paginación + Filtrado + Ordenación](#5️⃣-paginación--filtrado--ordenación)
6. [Middleware de Roles](#6️⃣-middleware-de-roles)
7. [Relaciones Eloquent](#7️⃣-relaciones-eloquent)
8. [Modelos Completos](#8️⃣-modelos-completos)
9. [Migraciones y Seeders](#9️⃣-migraciones-y-seeders)
10. [Rutas (routes/web.php)](#🔟-rutas-routeswebphp)
11. [Vistas Blade](#1️⃣1️⃣-vistas-blade)
12. [Subida de Imágenes](#1️⃣2️⃣-subida-de-imágenes)
13. [Comandos Artisan](#1️⃣3️⃣-comandos-artisan)
14. [Checklist Nueva App](#1️⃣4️⃣-checklist-nueva-app)
15. [Clase Custom (Edición Temporal)](#1️⃣5️⃣-clase-custom-edición-temporal)
16. [ImageController (Imágenes Privadas)](#1️⃣6️⃣-image-controller-servir-imágenes-privadas)
17. [HomeController (Panel + Editar Perfil)](#1️⃣7️⃣-home-controller-panel-de-usuario--editar-perfil)
18. [Vistas de Usuario (Admin)](#1️⃣8️⃣-vistas-de-usuario-admin)
19. [Vistas Auth (Home + Edit)](#1️⃣9️⃣-vistas-auth-home--edit-perfil)
20. [Eliminación Masiva (DeleteGroup)](#2️⃣0️⃣-eliminación-masiva-deletegroup)
21. [Configuración .env](#2️⃣1️⃣-configuración-env)
22. [🎯 REQUISITOS: App Vacacional](#2️⃣2️⃣--requisitos-proyecto-final-app-vacacional)

---

## 1️⃣ CONSIDERACIONES OBLIGATORIAS

| #   | Consideración                    | Descripción                                      |
| --- | -------------------------------- | ------------------------------------------------ |
| 1   | **Try...Catch en BD**            | TODAS las operaciones de BD deben usar try-catch |
| 2   | **Validación**                   | SIEMPRE validar datos (FormRequest o Validator)  |
| 3   | **Paginación + Filtros + Orden** | Listados NUNCA sin paginar                       |
| 4   | **Middleware de roles**          | admin, advanced, user                            |
| 5   | **Relaciones Eloquent**          | hasMany, belongsTo                               |
| 6   | **Migraciones + Seeders**        | Con FK y datos de prueba                         |
| 7   | **Originalidad**                 | Código limpio y funcional                        |

---

## 2️⃣ ESTRUCTURA DE CARPETAS

```
NombreApp/
├── app/
│   ├── Custom/                    # Clases personalizadas
│   ├── Http/
│   │   ├── Controllers/           # Controladores CRUD
│   │   ├── Middleware/            # AdminMiddleware, AdvancedMiddleware
│   │   └── Requests/              # Form Requests (validación)
│   ├── Models/                    # Modelos Eloquent
│   └── Providers/
├── database/
│   ├── factories/                 # Factories para testing
│   ├── migrations/                # Migraciones de BD
│   └── seeders/                   # Seeders de datos
├── resources/views/
│   ├── auth/                      # Login, Register, Home, Edit
│   ├── [entidad]/                 # index, create, edit, show
│   ├── layouts/                   # app.blade.php
│   └── template/                  # base.blade.php
├── routes/web.php                 # Rutas
├── .env                           # Configuración
└── composer.json                  # Dependencias
```

---

## 3️⃣ TRY...CATCH EN OPERACIONES BD

### STORE (Crear)

```php
function store(VacacionCreateRequest $request): RedirectResponse {
    $result = false;
    $vacacion = new Vacacion($request->all());
    $vacacion->iduser = Auth::user()->id;

    try {
        $result = $vacacion->save();
        $message = 'La vacación ha sido creada.';
    } catch(UniqueConstraintViolationException $e) {
        $message = 'Ya existe un registro con estos datos.';
    } catch(QueryException $e) {
        $message = 'Error en la consulta SQL.';
    } catch(\Exception $e) {
        $message = 'Se ha producido un error.';
    }

    $messageArray = ['general' => $message];

    if($result) {
        return redirect()->route('vacacion.index')->with($messageArray);
    } else {
        return back()->withInput()->withErrors($messageArray);
    }
}
```

### UPDATE (Actualizar)

```php
function update(VacacionEditRequest $request, Vacacion $vacacion): RedirectResponse {
    if(!$this->ownerControl($vacacion)) {
        return redirect()->route('main.index');
    }
    $result = false;

    try {
        $result = $vacacion->update($request->all());
        $message = 'La vacación ha sido editada.';
    } catch(UniqueConstraintViolationException $e) {
        $message = 'Ya existe un registro con estos datos.';
    } catch(\Exception $e) {
        $message = 'Se ha producido un error.';
    }

    $messageArray = ['general' => $message];

    if($result) {
        return redirect()->route('vacacion.edit', $vacacion->id)->with($messageArray);
    } else {
        return back()->withInput()->withErrors($messageArray);
    }
}
```

### DESTROY (Eliminar)

```php
function destroy(Vacacion $vacacion): RedirectResponse {
    if(!$this->ownerControl($vacacion)) {
        return redirect()->route('main.index');
    }
    try {
        $result = $vacacion->delete();
        $message = 'La vacación ha sido eliminada.';
    } catch(\Exception $e) {
        $result = false;
        $message = 'No se ha podido eliminar.';
    }
    $messageArray = ['general' => $message];
    if($result) {
        return redirect()->route('vacacion.index')->with($messageArray);
    } else {
        return back()->withInput()->withErrors($messageArray);
    }
}
```

### Excepciones a Capturar

```php
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;

catch(UniqueConstraintViolationException $e) { }  // Clave única
catch(QueryException $e) { }                       // Error SQL
catch(\Exception $e) { }                           // Cualquier error
```

---

## 4️⃣ VALIDACIÓN DE ENTRADAS

### Form Request Completo

```php
<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VacacionCreateRequest extends FormRequest
{
    function attributes(): array {
        return [
            'titulo' => 'título de la vacación',
            'descripcion' => 'descripción',
            'precio' => 'precio',
            'pais' => 'país',
            'idtipo' => 'tipo de vacación',
        ];
    }

    function authorize(): bool {
        return true;
    }

    function messages(): array {
        $max = 'El campo :attribute no puede tener más de :max caracteres.';
        $min = 'El campo :attribute no puede tener menos de :min caracteres.';
        $required = 'El campo :attribute es obligatorio.';
        $numeric = 'El campo :attribute debe ser numérico.';
        return [
            'titulo.required'  => $required,
            'titulo.min'       => $min,
            'titulo.max'       => $max,
            'descripcion.required' => $required,
            'precio.required'  => $required,
            'precio.numeric'   => $numeric,
            'pais.required'    => $required,
            'idtipo.required'  => $required,
        ];
    }

    function rules(): array {
        return [
            'titulo'      => 'required|min:4|max:100|string',
            'descripcion' => 'required|min:10',
            'precio'      => 'required|numeric|min:0',
            'pais'        => 'required|min:2|max:100',
            'idtipo'      => 'required',
        ];
    }
}
```

### Validador Manual en Controlador

```php
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

$rules = [
    'titulo' => ['required', 'min:4', 'max:100'],
    'precio' => ['required', 'numeric', 'min:0'],
];

$validator = Validator::make($request->all(), $rules, []);

if($validator->fails()) {
    return back()->withInput()->withErrors($validator);
}
```

### Reglas de Validación

```php
'required'          // Obligatorio
'nullable'          // Puede ser null
'string'            // Debe ser string
'min:4'             // Mínimo 4 caracteres
'max:60'            // Máximo 60 caracteres
'numeric'           // Debe ser número
'integer'           // Debe ser entero
'image'             // Debe ser imagen
'max:1024'          // Máximo 1024 KB (archivos)
'unique:tabla'      // Único en tabla
'email'             // Formato email
'confirmed'         // Campo_confirmation debe coincidir
'current_password'  // Contraseña actual válida
'exists:tabla,id'   // Debe existir en tabla
```

### Sanitización de Inputs

```php
private function limpiarCampo($campo): string {
    return $this->limpiarInput($campo, ['recent', 'titulo', 'precio', 'pais']);
}

private function limpiarOrden($orden): string {
    return $this->limpiarInput($orden, ['desc', 'asc']);
}

private function limpiarInput($input, array $array): string {
    $valor = $array[0];
    if (in_array($input, $array)) {
        $valor = $input;
    }
    return $valor;
}

private function limpiarNumeros($numero): mixed {
    if (is_numeric($numero)) {
        return $numero;
    }
    return null;
}
```

---

## 5️⃣ PAGINACIÓN + FILTRADO + ORDENACIÓN

### Controlador Index Completo

```php
function index(Request $request): View
{
    // 1. LIMPIAR INPUTS
    $campo = $this->limpiarCampo($request->campo);
    $orden = $this->limpiarOrden($request->orden);
    $q = $request->q;
    $idtipo = $request->idtipo;
    $precioMin = $this->limpiarNumeros($request->precioMin);
    $precioMax = $this->limpiarNumeros($request->precioMax);

    // 2. QUERY BASE CON JOIN
    $query = Vacacion::query();
    $query->join('tipo', 'tipo.id', '=', 'vacacion.idtipo')
          ->select('vacacion.*', 'tipo.nombre as tipo_nombre');

    // 3. FILTROS CONDICIONALES
    if ($precioMin != null) {
        $query->where('vacacion.precio', '>=', $precioMin);
    }
    if ($precioMax != null) {
        $query->where('vacacion.precio', '<=', $precioMax);
    }
    if ($idtipo != null) {
        $query->where('vacacion.idtipo', '=', $idtipo);
    }

    // 4. BÚSQUEDA EN MÚLTIPLES CAMPOS
    if ($q != null) {
        $query->where(function ($sq) use ($q) {
            $sq->where('vacacion.titulo', 'like', '%' . $q . '%')
                ->orWhere('vacacion.descripcion', 'like', '%' . $q . '%')
                ->orWhere('vacacion.pais', 'like', '%' . $q . '%')
                ->orWhere('tipo.nombre', 'like', '%' . $q . '%');
        });
    }

    // 5. ORDENACIÓN
    $campoorden = $this->getOrderBy($campo);
    $query->orderBy($campoorden, $orden);

    // 6. PAGINACIÓN
    $vacaciones = $query->paginate(10)->withQueryString();

    // 7. DATOS PARA FILTROS
    $tipos = Tipo::pluck('nombre', 'id')->all();

    return view('vacacion.index', [
        'vacaciones' => $vacaciones,
        'campo' => $campo,
        'precioMin' => $precioMin,
        'tipos' => $tipos,
        'precioMax' => $precioMax,
        'idtipo' => $idtipo,
        'orden' => $orden,
        'q' => $q,
    ]);
}

private function getOrderBy($orderRequest): string {
    $array = [
        'recent' => 'vacacion.id',
        'titulo' => 'vacacion.titulo',
        'precio' => 'vacacion.precio',
        'pais' => 'vacacion.pais'
    ];
    return $array[$orderRequest];
}
```

### Vista con Paginación y Filtros

```blade
@extends('template.base')

@section('modal')
{{-- Modal Ordenación --}}
<div class="modal fade" id="orderModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Ordenar por ...</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <ul>
          <li><a href="{{ route('main.index', array_merge(['campo' => 'recent', 'orden' => 'desc'], request()->except(['page','campo','orden']))) }}" class="btn btn-outline-primary mb-1">Más recientes</a></li>
          <li><a href="{{ route('main.index', array_merge(['campo' => 'precio', 'orden' => 'asc'], request()->except(['page','campo','orden']))) }}" class="btn btn-outline-primary mb-1">Precio: menor a mayor</a></li>
          <li><a href="{{ route('main.index', array_merge(['campo' => 'precio', 'orden' => 'desc'], request()->except(['page','campo','orden']))) }}" class="btn btn-outline-primary mb-1">Precio: mayor a menor</a></li>
        </ul>
      </div>
    </div>
  </div>
</div>

{{-- Modal Filtrado --}}
<div class="modal fade" id="filterModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Filtrar por ...</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('main.index') }}" method="get">
          <input type="hidden" name="campo" value="{{ $campo }}">
          <input type="hidden" name="orden" value="{{ $orden }}">

          <select name="idtipo" class="form-control">
            <option value="" @if($idtipo==null) selected @endif>Todos los tipos</option>
            @foreach($tipos as $i => $tipo)
            <option value="{{ $i }}" @if($i==$idtipo) selected @endif>{{ $tipo }}</option>
            @endforeach
          </select>

          <div class="mt-3">
            <label class="form-label fw-bold">Rango de precios</label>
            <div class="input-group mb-2">
              <span class="input-group-text">Mín €</span>
              <input type="number" name="precioMin" value="{{ $precioMin }}" class="form-control">
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text">Máx €</span>
              <input type="number" name="precioMax" value="{{ $precioMax }}" class="form-control">
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Filtrar</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('content')
<div class="row mb-4">
  <a class="btn btn-info mb-2" data-bs-toggle="modal" data-bs-target="#orderModal">Ordenar</a>
  <a class="btn btn-info mb-2" data-bs-toggle="modal" data-bs-target="#filterModal">Filtrar</a>
</div>

<div class="row row-cols-1 row-cols-md-3 g-3">
  @foreach($vacaciones as $vacacion)
  <div class="col">
    <div class="card">
      <div class="card-body">
        <h5>{{ $vacacion->titulo }}</h5>
        <p>{{ $vacacion->descripcion }}</p>
        <p><strong>{{ $vacacion->precio }} €</strong> - {{ $vacacion->pais }}</p>
        <a href="{{ route('vacacion.show', $vacacion->id) }}" class="btn btn-success">Ver</a>
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- PAGINACIÓN --}}
<div class="row mt-3">
  {{ $vacaciones->onEachSide(2)->links() }}
</div>
@endsection
```

---

## 6️⃣ MIDDLEWARE DE ROLES

### AdminMiddleware.php

```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if($user != null && $user->rol == 'admin') {
            return $next($request);
        } else {
            return redirect()->route('main.index');
        }
    }
}
```

### AdvancedMiddleware.php

```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdvancedMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if($user != null && ($user->rol == 'admin' || $user->rol == 'advanced')) {
            return $next($request);
        } else {
            return redirect()->route('main.index');
        }
    }
}
```

### Usar en Controlador

```php
use App\Http\Middleware\AdminMiddleware;

class UserController extends Controller {
    function __construct() {
        $this->middleware(AdminMiddleware::class);
    }
}

class VacacionController extends Controller {
    function __construct() {
        $this->middleware('verified')->except(['index', 'show']);
    }
}
```

### Control de Propietario

```php
private function ownerControl(Comentario $comentario): bool {
    $user = Auth::user();
    return $user->id == $comentario->iduser || $user->rol == 'admin';
}

function edit(Comentario $comentario): RedirectResponse|View {
    if(!$this->ownerControl($comentario)) {
        return redirect()->route('main.index');
    }
    // ...
}
```

### En Vistas

```blade
@if(Auth::user() != null && Auth::user()->isAdmin())
    <a href="{{ route('user.index') }}">Usuarios</a>
@endif

@if(Auth::user()->isAdmin() || $comentario->iduser == Auth::user()->id)
    <a href="{{ route('comentario.edit', $comentario->id) }}">Editar</a>
@endif
```

---

## 7️⃣ RELACIONES ELOQUENT

### HasMany (Uno a Muchos)

```php
use Illuminate\Database\Eloquent\Relations\HasMany;

// Tipo tiene muchas vacaciones
function vacaciones(): HasMany {
    return $this->hasMany('App\Models\Vacacion', 'idtipo');
}

// Vacacion tiene muchas fotos
function fotos(): HasMany {
    return $this->hasMany('App\Models\Foto', 'idvacacion');
}

// Vacacion tiene muchos comentarios
function comentarios(): HasMany {
    return $this->hasMany('App\Models\Comentario', 'idvacacion');
}

// Vacacion tiene muchas reservas
function reservas(): HasMany {
    return $this->hasMany('App\Models\Reserva', 'idvacacion');
}

// User tiene muchas reservas
function reservas(): HasMany {
    return $this->hasMany('App\Models\Reserva', 'iduser');
}
```

### BelongsTo (Muchos a Uno)

```php
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Vacacion pertenece a tipo
function tipo(): BelongsTo {
    return $this->belongsTo('App\Models\Tipo', 'idtipo');
}

// Foto pertenece a vacacion
function vacacion(): BelongsTo {
    return $this->belongsTo('App\Models\Vacacion', 'idvacacion');
}

// Reserva pertenece a user
function user(): BelongsTo {
    return $this->belongsTo('App\Models\User', 'iduser');
}

// Reserva pertenece a vacacion
function vacacion(): BelongsTo {
    return $this->belongsTo('App\Models\Vacacion', 'idvacacion');
}

// Comentario pertenece a user
function user(): BelongsTo {
    return $this->belongsTo('App\Models\User', 'iduser');
}

// Comentario pertenece a vacacion
function vacacion(): BelongsTo {
    return $this->belongsTo('App\Models\Vacacion', 'idvacacion');
}
```

---

## 8️⃣ MODELOS COMPLETOS

### Modelo Vacacion

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vacacion extends Model
{
    use HasFactory;

    protected $table = 'vacacion';

    protected $fillable = [
        'titulo', 'descripcion', 'precio', 'pais', 'idtipo',
    ];

    function tipo(): BelongsTo {
        return $this->belongsTo('App\Models\Tipo', 'idtipo');
    }

    function fotos(): HasMany {
        return $this->hasMany('App\Models\Foto', 'idvacacion');
    }

    function comentarios(): HasMany {
        return $this->hasMany('App\Models\Comentario', 'idvacacion');
    }

    function reservas(): HasMany {
        return $this->hasMany('App\Models\Reserva', 'idvacacion');
    }

    function getPrimeraFoto(): string {
        $foto = $this->fotos->first();
        if ($foto != null) {
            return url('storage/' . $foto->ruta);
        }
        return url('assets/img/default.jpg');
    }
}
```

### Modelo Tipo

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tipo extends Model
{
    use HasFactory;

    protected $table = 'tipo';
    public $timestamps = false;
    protected $fillable = ['nombre'];

    function vacaciones(): HasMany {
        return $this->hasMany('App\Models\Vacacion', 'idtipo');
    }
}
```

### Modelo Foto

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Foto extends Model
{
    use HasFactory;

    protected $table = 'foto';
    protected $fillable = ['idvacacion', 'ruta'];

    function vacacion(): BelongsTo {
        return $this->belongsTo('App\Models\Vacacion', 'idvacacion');
    }
}
```

### Modelo Reserva

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reserva';
    protected $fillable = ['iduser', 'idvacacion'];

    function user(): BelongsTo {
        return $this->belongsTo('App\Models\User', 'iduser');
    }

    function vacacion(): BelongsTo {
        return $this->belongsTo('App\Models\Vacacion', 'idvacacion');
    }
}
```

### Modelo Comentario

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comentario extends Model
{
    use HasFactory;

    protected $table = 'comentario';
    protected $fillable = ['iduser', 'idvacacion', 'texto'];

    function user(): BelongsTo {
        return $this->belongsTo('App\Models\User', 'iduser');
    }

    function vacacion(): BelongsTo {
        return $this->belongsTo('App\Models\Vacacion', 'idvacacion');
    }
}
```

### Modelo User con Roles

```php
<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'rol'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    function reservas(): HasMany {
        return $this->hasMany('App\Models\Reserva', 'iduser');
    }

    function comentarios(): HasMany {
        return $this->hasMany('App\Models\Comentario', 'iduser');
    }

    function isRol($rol): bool {
        return $this->rol == $rol;
    }

    function isAdmin(): bool {
        return $this->isRol('admin');
    }

    function isAdvanced(): bool {
        return $this->isRol('advanced');
    }

    function isUser(): bool {
        return $this->isRol('user');
    }

    // Verificar si tiene reserva en una vacación
    function tieneReserva($idvacacion): bool {
        return $this->reservas()->where('idvacacion', $idvacacion)->exists();
    }
}
```

---

## 9️⃣ MIGRACIONES Y SEEDERS

### Migración: Tipo

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void {
        Schema::create('tipo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
        });
    }

    function down(): void {
        Schema::dropIfExists('tipo');
    }
};
```

### Migración: Vacacion

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void {
        Schema::create('vacacion', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 100);
            $table->text('descripcion');
            $table->decimal('precio', 10, 2);
            $table->string('pais', 100);
            $table->foreignId('idtipo');
            $table->timestamps();

            $table->foreign('idtipo')->references('id')->on('tipo');
        });
    }

    function down(): void {
        Schema::dropIfExists('vacacion');
    }
};
```

### Migración: Foto

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void {
        Schema::create('foto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idvacacion');
            $table->string('ruta', 255);
            $table->timestamps();

            $table->foreign('idvacacion')->references('id')->on('vacacion');
        });
    }

    function down(): void {
        Schema::dropIfExists('foto');
    }
};
```

### Migración: Reserva

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void {
        Schema::create('reserva', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iduser');
            $table->foreignId('idvacacion');
            $table->timestamps();

            $table->foreign('iduser')->references('id')->on('users');
            $table->foreign('idvacacion')->references('id')->on('vacacion');

            // Un usuario solo puede reservar una vez la misma vacación
            $table->unique(['iduser', 'idvacacion']);
        });
    }

    function down(): void {
        Schema::dropIfExists('reserva');
    }
};
```

### Migración: Comentario

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void {
        Schema::create('comentario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iduser');
            $table->foreignId('idvacacion');
            $table->text('texto');
            $table->timestamps();

            $table->foreign('iduser')->references('id')->on('users');
            $table->foreign('idvacacion')->references('id')->on('vacacion');
        });
    }

    function down(): void {
        Schema::dropIfExists('comentario');
    }
};
```

### Migración: Añadir rol a users

```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->string('rol', 20)->default('user');
        });
    }

    function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('rol');
        });
    }
};
```

### Tipos de Columnas

```php
$table->id();                              // BIGINT AUTO_INCREMENT
$table->string('campo', 100);              // VARCHAR(100)
$table->string('campo', 100)->unique();    // VARCHAR UNIQUE
$table->string('campo', 100)->nullable();  // VARCHAR NULL
$table->text('campo');                     // TEXT
$table->longText('campo');                 // LONGTEXT
$table->decimal('campo', 10, 2);           // DECIMAL(10,2)
$table->boolean('campo')->nullable();      // BOOLEAN NULL
$table->foreignId('idcampo');              // BIGINT UNSIGNED (FK)
$table->timestamps();                      // created_at, updated_at
$table->unique(['campo1', 'campo2']);      // UNIQUE compuesto
```

### DatabaseSeeder.php

```php
<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'rol' => 'admin'
        ]);

        // Users
        $users = User::factory(10)->create();

        // Tipos
        $tipos = \App\Models\Tipo::factory(5)->create();

        // Vacaciones
        \App\Models\Vacacion::factory(30)->create();
    }
}
```

### Seeder con Faker

```php
<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoSeeder extends Seeder
{
    function run(): void {
        $tipos = ['Playa', 'Montaña', 'Ciudad', 'Aventura', 'Crucero', 'Rural'];

        foreach($tipos as $tipo) {
            DB::table("tipo")->insert([
                "nombre" => $tipo,
            ]);
        }
    }
}
```

### Factory

```php
<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Vacacion;
use App\Models\Tipo;

class VacacionFactory extends Factory
{
    protected $model = Vacacion::class;

    public function definition(): array {
        return [
            'titulo' => $this->faker->sentence(3),
            'descripcion' => $this->faker->paragraphs(2, true),
            'precio' => $this->faker->randomFloat(2, 100, 5000),
            'pais' => $this->faker->country(),
            'idtipo' => Tipo::inRandomOrder()->first()->id,
        ];
    }
}
```

---

## 🔟 RUTAS (routes/web.php)

```php
<?php
use App\Http\Controllers\VacacionController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Página principal - Ver todas las ofertas
Route::get('/', [MainController::class, 'index'])->name('main.index');

// Resources CRUD
Route::resource('vacacion', VacacionController::class);
Route::resource('tipo', TipoController::class);
Route::resource('user', UserController::class);

// Reservas (requiere usuario verificado)
Route::post('reserva', [ReservaController::class, 'store'])->name('reserva.store');
Route::delete('reserva/{reserva}', [ReservaController::class, 'destroy'])->name('reserva.destroy');

// Comentarios (solo si tiene reserva)
Route::post('comentario', [ComentarioController::class, 'store'])->name('comentario.store');
Route::get('comentario/{comentario}/edit', [ComentarioController::class, 'edit'])->name('comentario.edit');
Route::put('comentario/{comentario}', [ComentarioController::class, 'update'])->name('comentario.update');
Route::delete('comentario/{comentario}', [ComentarioController::class, 'destroy'])->name('comentario.destroy');

// Autenticación
Auth::routes(['verify' => true]);

Route::get('home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
```

### Rutas de Resource

| Método    | URI                       | Acción  | Nombre           |
| --------- | ------------------------- | ------- | ---------------- |
| GET       | /vacacion                 | index   | vacacion.index   |
| GET       | /vacacion/create          | create  | vacacion.create  |
| POST      | /vacacion                 | store   | vacacion.store   |
| GET       | /vacacion/{vacacion}      | show    | vacacion.show    |
| GET       | /vacacion/{vacacion}/edit | edit    | vacacion.edit    |
| PUT/PATCH | /vacacion/{vacacion}      | update  | vacacion.update  |
| DELETE    | /vacacion/{vacacion}      | destroy | vacacion.destroy |

---

## 1️⃣1️⃣ VISTAS BLADE

### Template Base (template/base.blade.php)

```blade
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Vacaciones')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <a class="navbar-brand" href="{{ route('main.index') }}">🌴 Vacaciones</a>
        <div class="collapse navbar-collapse">
          <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link" href="{{ route('main.index') }}">Ofertas</a></li>
            @auth
            <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Mis Reservas</a></li>
            @endauth
            @if(Auth::user() != null && Auth::user()->isAdmin())
            <li class="nav-item"><a class="nav-link" href="{{ route('vacacion.create') }}">Nueva Oferta</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('user.index') }}">Usuarios</a></li>
            @endif
          </ul>

          <form class="d-flex" method="get" action="{{ route('main.index') }}">
            @foreach(request()->except(['page','q']) as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <input name="q" class="form-control me-2" type="search" value="{{ $q ?? '' }}" placeholder="Buscar...">
            <button class="btn btn-outline-success" type="submit">Buscar</button>
          </form>

          <ul class="navbar-nav ms-2">
            @guest
            <li><a class="btn btn-outline-light me-2" href="{{ route('login') }}">Login</a></li>
            <li><a class="btn btn-success" href="{{ route('register') }}">Registrarse</a></li>
            @else
            <li><a class="btn btn-outline-light me-2" href="{{ route('home') }}">{{ Auth::user()->name }}</a></li>
            <li>
              <form method="post" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger">Salir</button>
              </form>
            </li>
            @endguest
          </ul>
        </div>
      </div>
    </nav>

    <div class="container my-5">
        @error('general')
        <div class="alert alert-danger">{{ $message }}</div>
        @enderror

        @if(session('general'))
        <div class="alert alert-success">{{ session('general') }}</div>
        @endif

        @yield('modal')
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
```

### Vista Show (vacacion/show.blade.php)

```blade
@extends('template.base')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h1>{{ $vacacion->titulo }}</h1>
        <p class="lead">{{ $vacacion->descripcion }}</p>
        <p><strong>País:</strong> {{ $vacacion->pais }}</p>
        <p><strong>Tipo:</strong> {{ $vacacion->tipo->nombre }}</p>
        <h3 class="text-success">{{ number_format($vacacion->precio, 2) }} €</h3>

        {{-- Galería de fotos --}}
        <div class="row mt-4">
            @foreach($vacacion->fotos as $foto)
            <div class="col-md-4 mb-3">
                <img src="{{ url('storage/' . $foto->ruta) }}" class="img-fluid rounded">
            </div>
            @endforeach
        </div>

        {{-- Botón Reservar (solo usuarios verificados sin reserva) --}}
        @auth
            @if(Auth::user()->hasVerifiedEmail())
                @if(!Auth::user()->tieneReserva($vacacion->id))
                <form action="{{ route('reserva.store') }}" method="post" class="mt-4">
                    @csrf
                    <input type="hidden" name="idvacacion" value="{{ $vacacion->id }}">
                    <button type="submit" class="btn btn-lg btn-success">Reservar Ahora</button>
                </form>
                @else
                <div class="alert alert-info mt-4">✓ Ya tienes una reserva para esta vacación</div>
                @endif
            @else
            <div class="alert alert-warning mt-4">Verifica tu email para poder reservar</div>
            @endif
        @else
        <div class="alert alert-warning mt-4">
            <a href="{{ route('login') }}">Inicia sesión</a> o
            <a href="{{ route('register') }}">regístrate</a> para reservar
        </div>
        @endauth
    </div>
</div>

{{-- COMENTARIOS --}}
<div class="row mt-5">
    <h4>Comentarios</h4>

    @foreach($vacacion->comentarios as $comentario)
    <div class="card mb-2">
        <div class="card-body">
            <p>{{ $comentario->texto }}</p>
            <small class="text-muted">Por {{ $comentario->user->name }} - {{ $comentario->created_at->format('d/m/Y') }}</small>

            {{-- Solo el autor puede editar --}}
            @auth
            @if(Auth::user()->id == $comentario->iduser || Auth::user()->isAdmin())
            <a href="{{ route('comentario.edit', $comentario->id) }}" class="btn btn-sm btn-warning">Editar</a>
            @endif
            @endauth
        </div>
    </div>
    @endforeach

    {{-- Formulario comentario (solo si tiene reserva) --}}
    @auth
        @if(Auth::user()->tieneReserva($vacacion->id))
        <form action="{{ route('comentario.store') }}" method="post" class="mt-3">
            @csrf
            <input type="hidden" name="idvacacion" value="{{ $vacacion->id }}">
            <div class="mb-3">
                <textarea name="texto" class="form-control" rows="3" placeholder="Escribe tu comentario..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Comentario</button>
        </form>
        @else
        <div class="alert alert-info mt-3">Debes reservar esta vacación para poder comentar</div>
        @endif
    @endauth
</div>
@endsection
```

### Directivas Blade

```blade
{{-- Variables --}}
{{ $variable }}
{!! $html !!}

{{-- Condicionales --}}
@if(condicion)
@elseif(otra)
@else
@endif

{{-- Auth --}}
@guest @endguest
@auth @endauth

{{-- Loops --}}
@foreach($items as $item) @endforeach

{{-- Errores --}}
@error('campo')
<div class="alert alert-danger">{{ $message }}</div>
@enderror

{{-- Session --}}
@if(session('general'))
<div class="alert alert-success">{{ session('general') }}</div>
@endif

{{-- Formularios --}}
@csrf
@method('put')
@method('delete')

{{-- Old values --}}
value="{{ old('campo') }}"
value="{{ old('campo', $entidad->campo) }}"

{{-- Rutas --}}
{{ route('vacacion.show', $vacacion->id) }}
{{ url('storage/' . $path) }}

{{-- Números --}}
{{ number_format($precio, 2) }} €

{{-- Fechas --}}
{{ $item->created_at->format('d/m/Y') }}
```

---

## 1️⃣2️⃣ SUBIDA DE IMÁGENES

### Controlador

```php
private function upload(Request $request, $idvacacion): string|null {
    $path = null;
    if($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
        $image = $request->file('imagen');
        $fileName = $idvacacion . '_' . time() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('fotos', $fileName, 'public');
    }
    return $path;
}
```

---

## 1️⃣3️⃣ COMANDOS ARTISAN

```bash
# Crear modelo con migración
php artisan make:model NombreModelo -m

# Crear controlador resource
php artisan make:controller NombreController --resource

# Crear Form Request
php artisan make:request NombreRequest

# Crear middleware
php artisan make:middleware NombreMiddleware

# Crear seeder
php artisan make:seeder NombreSeeder

# Crear factory
php artisan make:factory NombreFactory

# Ejecutar migraciones
php artisan migrate

# Refrescar BD con seeders
php artisan migrate:fresh --seed

# Crear link de storage
php artisan storage:link

# Servidor
php artisan serve
```

---

## 1️⃣4️⃣ CHECKLIST NUEVA APP

1. [ ] `composer create-project laravel/laravel NombreApp`
2. [ ] Configurar `.env` (BD, APP_NAME)
3. [ ] `composer require laravel/ui` + `php artisan ui bootstrap --auth`
4. [ ] `npm install && npm run build`
5. [ ] Crear modelos: `php artisan make:model Entidad -m`
6. [ ] Definir `$fillable` y relaciones en modelos
7. [ ] Completar migraciones con columnas y FK
8. [ ] `php artisan migrate`
9. [ ] Crear factories y seeders
10. [ ] `php artisan db:seed`
11. [ ] Crear controladores: `php artisan make:controller EntidadController --resource`
12. [ ] Crear Form Requests
13. [ ] Crear middlewares y registrarlos
14. [ ] Definir rutas en `routes/web.php`
15. [ ] Implementar try-catch en TODAS las operaciones BD
16. [ ] Implementar paginación + filtros + ordenación
17. [ ] Crear vistas (template, index, create, edit, show)
18. [ ] `php artisan storage:link`
19. [ ] Probar todo

---

## 1️⃣5️⃣ CLASE CUSTOM (Edición Temporal)

### app/Custom/SentComments.php

```php
<?php

namespace App\Custom;

class SentComments
{
    // Verifica si un comentario fue enviado recientemente (editable por 10 min)
    public static function isComment($commentId): bool
    {
        $comments = session('sent_comments', []);

        if (!isset($comments[$commentId])) {
            return false;
        }

        $sentTime = $comments[$commentId];
        $now = time();
        $tenMinutes = 10 * 60; // 10 minutos en segundos

        return ($now - $sentTime) <= $tenMinutes;
    }

    // Registra un comentario como enviado
    public static function addComment($commentId): void
    {
        $comments = session('sent_comments', []);
        $comments[$commentId] = time();
        session(['sent_comments' => $comments]);
    }

    // Elimina un comentario del registro
    public static function removeComment($commentId): void
    {
        $comments = session('sent_comments', []);
        unset($comments[$commentId]);
        session(['sent_comments' => $comments]);
    }
}
```

### Uso en Modelo Comentario

```php
<?php
namespace App\Models;

use App\Custom\SentComments;

class Comentario extends Model
{
    // ...

    // Método para verificar si es editable
    function isEditable(): bool
    {
        return SentComments::isComment($this->id);
    }
}
```

### Uso en Controlador

```php
use App\Custom\SentComments;

function store(Request $request): RedirectResponse
{
    // ... crear comentario ...

    try {
        $result = $comentario->save();
        // Registrar para edición temporal
        SentComments::addComment($comentario->id);
        $message = 'Comentario añadido.';
    } catch(\Exception $e) {
        $message = 'Error al guardar.';
    }
    // ...
}
```

---

## 1️⃣6️⃣ IMAGE CONTROLLER (Servir Imágenes Privadas)

### app/Http/Controllers/ImageController.php

```php
<?php

namespace App\Http\Controllers;

use App\Models\Vacacion;
use App\Models\Foto;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageController extends Controller
{
    // Servir imagen de vacación por ID
    function view($id): BinaryFileResponse
    {
        $vacacion = Vacacion::find($id);
        $foto = $vacacion?->fotos->first();

        if($vacacion == null || $foto == null ||
           !file_exists(storage_path('app/private') . '/' . $foto->ruta)) {
            return response()->file(base_path('public/assets/img/noimage.png'));
        }
        return response()->file(storage_path('app/private') . '/' . $foto->ruta);
    }

    // Servir foto específica por ID
    function foto($id): BinaryFileResponse
    {
        $foto = Foto::find($id);

        if($foto == null || $foto->ruta == null ||
           !file_exists(storage_path('app/private') . '/' . $foto->ruta)) {
            return response()->file(base_path('public/assets/img/noimage.png'));
        }
        return response()->file(storage_path('app/private') . '/' . $foto->ruta);
    }
}
```

### Ruta para ImageController

```php
// routes/web.php
Route::get('image/{id}', [ImageController::class, 'view'])->name('image.view');
Route::get('foto/{id}', [ImageController::class, 'foto'])->name('foto.view');
```

### Uso en Vistas

```blade
{{-- Con ruta nombrada --}}
<img src="{{ route('image.view', $vacacion->id) }}?r={{ rand(1, 1000) }}" width="140px">

{{-- Con URL directa a storage público --}}
<img src="{{ url('storage/' . $foto->ruta) }}" class="img-fluid">
```

---

## 1️⃣7️⃣ HOME CONTROLLER (Panel de Usuario + Editar Perfil)

### app/Http/Controllers/HomeController.php

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class HomeController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified')->except(['index']);
    }

    // Panel de usuario
    function index(): View
    {
        return view('auth.home');
    }

    // Formulario de edición
    function edit(): View
    {
        return view('auth.edit');
    }

    // Actualizar perfil
    function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $rules = [
            'current-password' => 'current_password',
            'email'            => 'required|max:255|email|unique:users,email,' . $user->id,
            'name'             => 'required|max:255',
            'password'         => 'nullable|min:8|confirmed',
        ];

        $messages = [
            'name.required'                     => 'Nombre obligatorio',
            'name.max'                          => 'Nombre máximo 255 caracteres',
            'email.max'                         => 'Correo máximo 255 caracteres',
            'email.unique'                      => 'Este correo ya está en uso',
            'email.required'                    => 'Correo obligatorio',
            'email.email'                       => 'Formato de correo inválido',
            'password.min'                      => 'La contraseña debe tener mínimo 8 caracteres',
            'password.confirmed'                => 'Las contraseñas no coinciden',
            'current-password.current_password' => 'La contraseña actual es incorrecta'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $user->name = $request->name;

        // Si cambia el email, quitar verificación
        if($user->email != $request->email) {
            $user->email_verified_at = null;
            $user->email = $request->email;
        }

        // Cambiar contraseña solo si se proporciona
        if($request->password != null) {
            $user->password = Hash::make($request->password);
        }

        try {
            $user->save();
            $message = 'Perfil actualizado correctamente.';
        } catch(\Exception $e) {
            $message = 'Error al guardar.';
        }

        return redirect()->route('home')->with(['general' => $message]);
    }
}
```

### Rutas para Home

```php
// routes/web.php
Route::get('home', [HomeController::class, 'index'])->name('home');
Route::get('home/edit', [HomeController::class, 'edit'])->name('home.edit');
Route::put('home/update', [HomeController::class, 'update'])->name('home.update');
```

---

## 1️⃣8️⃣ VISTAS DE USUARIO (Admin)

### resources/views/user/index.blade.php

```blade
@extends('template.base')

@section('content')

{{-- Modal de confirmación --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Confirmar eliminación</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        ¿Seguro que quieres borrar al usuario <span id="modal-user-name">XXX</span>?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button form="form-delete" type="submit" class="btn btn-danger">Eliminar</button>
      </div>
    </div>
  </div>
</div>

<h2>Gestión de Usuarios</h2>

<table class="table table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Verificado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->rol }}</td>
            <td>@if($user->hasVerifiedEmail()) ✓ @else ✗ @endif</td>
            <td>
                <a href="{{ route('user.show', $user->id) }}" class="btn btn-sm btn-success">Ver</a>
                <a href="{{ route('user.edit', $user->id) }}" class="btn btn-sm btn-warning">Editar</a>
                <a data-title="{{ $user->name }}"
                   data-href="{{ route('user.destroy', $user->id) }}"
                   class="btn btn-sm btn-danger"
                   data-bs-toggle="modal"
                   data-bs-target="#deleteModal">Eliminar</a>
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4">Total usuarios:</th>
            <th>{{ count($users) }}</th>
            <th></th>
        </tr>
    </tfoot>
</table>

<form id="form-delete" action="" method="post">
    @csrf
    @method('delete')
</form>

@endsection

@section('scripts')
<script>
// Script para el modal de eliminación
document.querySelectorAll('[data-bs-target="#deleteModal"]').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('modal-user-name').textContent = this.dataset.title;
        document.getElementById('form-delete').action = this.dataset.href;
    });
});
</script>
@endsection
```

### resources/views/user/create.blade.php

```blade
@extends('template.base')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Crear usuario</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user.store') }}">
                        @csrf
                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">Nombre</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                       name="name" value="{{ old('name') }}" required autofocus>
                                @error('name')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">Correo</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">Contraseña</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                       name="password" required>
                                @error('password')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="rol" class="col-md-4 col-form-label text-md-end">Rol</label>
                            <div class="col-md-6">
                                <select required name="rol" id="rol" class="form-control">
                                    <option value="" @if(old('rol') == null) selected @endif disabled>Selecciona...</option>
                                    @foreach($rols as $rol)
                                        <option value="{{ $rol }}" @if($rol == old('rol')) selected @endif>{{ $rol }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">Crear usuario</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

### resources/views/user/edit.blade.php

```blade
@extends('template.base')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Editar usuario: {{ $user->name }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user.update', $user->id) }}">
                        @csrf
                        @method('put')

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">Nombre</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                       name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">Correo</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">Nueva Contraseña</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password">
                                <small class="text-muted">Dejar vacío para mantener la actual</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="rol" class="col-md-4 col-form-label text-md-end">Rol</label>
                            <div class="col-md-6">
                                <select required name="rol" id="rol" class="form-control">
                                    @foreach($rols as $rol)
                                        <option value="{{ $rol }}" @if($rol == old('rol', $user->rol)) selected @endif>{{ $rol }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">Verificado</label>
                            <div class="col-md-6">
                                <div class="form-check form-switch mt-2">
                                    <input type="checkbox" class="form-check-input" id="verified" name="verified" value="1"
                                           @if($user->email_verified_at != null) checked @endif>
                                    <label class="form-check-label" for="verified">Email verificado</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                <a href="{{ route('user.index') }}" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

---

## 1️⃣9️⃣ VISTAS AUTH (Home + Edit Perfil)

### resources/views/auth/home.blade.php

```blade
@extends('template.base')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Panel de usuario</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p><strong>Nombre:</strong> {{ Auth::user()->name }}</p>
                    <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                    <p><strong>Rol:</strong> {{ Auth::user()->rol }}</p>
                    <p><strong>Verificado:</strong>
                        @if(Auth::user()->hasVerifiedEmail())
                            <span class="badge bg-success">Sí</span>
                        @else
                            <span class="badge bg-danger">No</span>
                        @endif
                    </p>

                    <hr>

                    <a href="{{ route('home.edit') }}" class="btn btn-primary">Editar perfil</a>
                </div>
            </div>

            {{-- Mis reservas --}}
            @if(Auth::user()->reservas->count() > 0)
            <div class="card mt-4">
                <div class="card-header">Mis Reservas</div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach(Auth::user()->reservas as $reserva)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ route('vacacion.show', $reserva->idvacacion) }}">
                                {{ $reserva->vacacion->titulo }}
                            </a>
                            <span class="badge bg-primary">{{ number_format($reserva->vacacion->precio, 2) }} €</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
```

### resources/views/auth/edit.blade.php

```blade
@extends('template.base')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Editar mi perfil</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('home.update') }}">
                        @csrf
                        @method('put')

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">Nombre</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                       name="name" value="{{ old('name', Auth::user()->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">Email</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email', Auth::user()->email) }}" required>
                                @error('email')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                                <small class="text-muted">Si cambias el email, deberás verificarlo de nuevo.</small>
                            </div>
                        </div>

                        <hr>
                        <p class="text-muted">Cambiar contraseña (opcional)</p>

                        <div class="row mb-3">
                            <label for="current-password" class="col-md-4 col-form-label text-md-end">Contraseña actual</label>
                            <div class="col-md-6">
                                <input id="current-password" type="password" class="form-control @error('current-password') is-invalid @enderror"
                                       name="current-password">
                                @error('current-password')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">Nueva contraseña</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                       name="password">
                                @error('password')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">Confirmar contraseña</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control"
                                       name="password_confirmation">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                <a href="{{ route('home') }}" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

---

## 2️⃣0️⃣ ELIMINACIÓN MASIVA (DeleteGroup)

### En Controlador

```php
function deleteGroup(Request $request): RedirectResponse
{
    $ids = $request->input('ids', []);

    if (empty($ids)) {
        return back()->withErrors(['general' => 'No se seleccionó ningún elemento.']);
    }

    try {
        $count = Vacacion::whereIn('id', $ids)->delete();
        $message = "Se han eliminado $count elementos.";
    } catch (\Exception $e) {
        $message = 'Error al eliminar los elementos.';
        return back()->withErrors(['general' => $message]);
    }

    return redirect()->route('vacacion.index')->with(['general' => $message]);
}
```

### Ruta

```php
Route::delete('vacacion/delete/group', [VacacionController::class, 'deleteGroup'])->name('vacacion.delete.group');
```

### Vista con Checkboxes

```blade
@extends('template.base')

@section('content')

{{-- Modal de confirmación masiva --}}
<div class="modal fade" id="deleteGroupModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Confirmar eliminación masiva</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        ¿Seguro que quieres eliminar los elementos seleccionados?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button form="form-delete-group" type="submit" class="btn btn-danger">Eliminar seleccionados</button>
      </div>
    </div>
  </div>
</div>

<form id="form-delete-group" action="{{ route('vacacion.delete.group') }}" method="post">
    @csrf
    @method('delete')

    <div class="mb-3">
        <a class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteGroupModal">
            Eliminar seleccionados
        </a>
    </div>

    <table class="table table-hover">
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th>#</th>
                <th>Título</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vacaciones as $vacacion)
            <tr>
                <td><input type="checkbox" name="ids[]" value="{{ $vacacion->id }}" class="item-checkbox"></td>
                <td>{{ $vacacion->id }}</td>
                <td>{{ $vacacion->titulo }}</td>
                <td>{{ $vacacion->precio }} €</td>
                <td>
                    <a href="{{ route('vacacion.show', $vacacion->id) }}" class="btn btn-sm btn-success">Ver</a>
                    <a href="{{ route('vacacion.edit', $vacacion->id) }}" class="btn btn-sm btn-warning">Editar</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</form>

{{ $vacaciones->links() }}

@endsection

@section('scripts')
<script>
// Seleccionar/Deseleccionar todos
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.item-checkbox').forEach(cb => {
        cb.checked = this.checked;
    });
});
</script>
@endsection
```

---

## 2️⃣1️⃣ CONFIGURACIÓN .ENV

### Archivo .env Ejemplo

```env
APP_NAME="Vacaciones App"
APP_ENV=local
APP_KEY=base64:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
APP_DEBUG=true
APP_URL=http://localhost

APP_LOCALE=es
APP_FALLBACK_LOCALE=es
APP_FAKER_LOCALE=es_ES

# Base de datos MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vacaciones
DB_USERNAME=root
DB_PASSWORD=

# Base de datos SQLite (alternativa)
# DB_CONNECTION=sqlite
# DB_DATABASE=/path/to/database.sqlite

# Sesiones
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Sistema de archivos
FILESYSTEM_DISK=local

# Correo (para verificación de email)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_correo@gmail.com
MAIL_PASSWORD=tu_password_de_aplicacion
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@vacaciones.com"
MAIL_FROM_NAME="${APP_NAME}"

# Alternativa: Log (desarrollo - no envía emails reales)
# MAIL_MAILER=log

# Cache
CACHE_STORE=database

# Cola de trabajos
QUEUE_CONNECTION=database
```

### Variables Importantes

| Variable          | Descripción            | Valores                          |
| ----------------- | ---------------------- | -------------------------------- |
| `APP_DEBUG`       | Modo debug             | `true` (dev) / `false` (prod)    |
| `APP_ENV`         | Entorno                | `local`, `staging`, `production` |
| `DB_CONNECTION`   | Driver de BD           | `mysql`, `sqlite`, `pgsql`       |
| `SESSION_DRIVER`  | Donde guardar sesiones | `database`, `file`, `cookie`     |
| `MAIL_MAILER`     | Driver de correo       | `smtp`, `log`, `sendmail`        |
| `FILESYSTEM_DISK` | Disco por defecto      | `local`, `public`, `s3`          |

### Configuración de BD con Docker

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=vacaciones
DB_USERNAME=vacaciones
DB_PASSWORD=vacaciones
```

### Crear archivo .env desde ejemplo

```bash
cp .env.example .env
php artisan key:generate
```

---

## 2️⃣2️⃣ 🎯 REQUISITOS: PROYECTO FINAL (APP VACACIONAL)

### 📅 Fecha de entrega: **3 de febrero**

### 📝 Descripción

**Venta de productos vacacionales** - Sistema de reservas de vacaciones donde los usuarios pueden ver ofertas, reservar y comentar.

---

### 🗄️ TABLAS DE LA BASE DE DATOS

#### Tabla: `vacacion`

| Campo       | Tipo          | Descripción            |
| ----------- | ------------- | ---------------------- |
| id          | BIGINT        | Primary Key            |
| titulo      | VARCHAR(100)  | Título de la oferta    |
| descripcion | TEXT          | Descripción detallada  |
| precio      | DECIMAL(10,2) | Precio en euros        |
| pais        | VARCHAR(100)  | País de destino        |
| idtipo      | FK → tipo     | Tipo de vacación       |
| timestamps  |               | created_at, updated_at |

#### Tabla: `tipo`

| Campo  | Tipo         | Descripción                            |
| ------ | ------------ | -------------------------------------- |
| id     | BIGINT       | Primary Key                            |
| nombre | VARCHAR(100) | Nombre del tipo (Playa, Montaña, etc.) |

#### Tabla: `foto`

| Campo      | Tipo          | Descripción            |
| ---------- | ------------- | ---------------------- |
| id         | BIGINT        | Primary Key            |
| idvacacion | FK → vacacion | Vacación asociada      |
| ruta       | VARCHAR(255)  | Ruta del archivo       |
| timestamps |               | created_at, updated_at |

#### Tabla: `users`

| Campo             | Tipo        | Descripción           |
| ----------------- | ----------- | --------------------- |
| id                | BIGINT      | Primary Key           |
| name              | VARCHAR     | Nombre                |
| email             | VARCHAR     | Email (unique)        |
| password          | VARCHAR     | Contraseña            |
| **rol**           | VARCHAR(20) | admin, advanced, user |
| email_verified_at | TIMESTAMP   | Verificación email    |
| ...               |             | Otros campos estándar |

#### Tabla: `reserva`

| Campo      | Tipo          | Descripción            |
| ---------- | ------------- | ---------------------- |
| id         | BIGINT        | Primary Key            |
| iduser     | FK → users    | Usuario que reserva    |
| idvacacion | FK → vacacion | Vacación reservada     |
| timestamps |               | created_at, updated_at |
| **UNIQUE** |               | (iduser, idvacacion)   |

#### Tabla: `comentario`

| Campo      | Tipo          | Descripción              |
| ---------- | ------------- | ------------------------ |
| id         | BIGINT        | Primary Key              |
| iduser     | FK → users    | Usuario que comenta      |
| idvacacion | FK → vacacion | Vacación comentada       |
| texto      | TEXT          | Contenido del comentario |
| timestamps |               | created_at, updated_at   |

---

### 🔐 REGLAS DE NEGOCIO

| Funcionalidad         | Requisito                                         |
| --------------------- | ------------------------------------------------- |
| **Ver ofertas**       | ✅ Cualquier visitante (sin login)                |
| **Ver comentarios**   | ✅ Cualquier visitante (sin login)                |
| **Hacer reserva**     | 🔒 Usuario **verificado** (email confirmado)      |
| **Hacer comentario**  | 🔒 Usuario con **reserva previa** en esa vacación |
| **Editar comentario** | 🔒 Solo el **autor** del comentario               |
| **Gestión usuarios**  | 🔒 Solo **admin**                                 |

---

### ⚠️ CONSIDERACIONES OBLIGATORIAS

1. ✅ **Operaciones BD con try...catch** - Todas las operaciones de BD envueltas
2. ✅ **Validación de entradas** - FormRequest o Validator
3. ✅ **Paginación + Filtrado + Ordenación** - En listado de vacaciones
4. ✅ **Middleware de roles** - admin, advanced, user
5. ✅ **Relaciones Eloquent** - hasMany, belongsTo
6. ✅ **Migraciones y Seeders** - Con FK y datos de prueba
7. ✅ **Originalidad, esfuerzo y dedicación**

---

### 📊 DIAGRAMA DE RELACIONES

```
┌─────────┐       ┌───────────┐       ┌──────────┐
│  tipo   │──1:N──│  vacacion │──1:N──│   foto   │
└─────────┘       └───────────┘       └──────────┘
                        │
                        │ 1:N
                        ▼
                  ┌───────────┐
                  │  reserva  │──N:1──┐
                  └───────────┘       │
                        │             │
                        │             ▼
                  ┌───────────┐   ┌───────┐
                  │comentario │──N:1│ users │
                  └───────────┘   └───────┘
```

---

### 🚀 FLUJO DE LA APLICACIÓN

1. **Visitante** → Ve ofertas y comentarios
2. **Registro** → Crea cuenta
3. **Verificación** → Confirma email
4. **Reserva** → Puede reservar vacaciones
5. **Comentar** → Puede comentar en vacaciones reservadas
6. **Editar** → Solo sus propios comentarios

---

> **IMPORTANTE**: Este proyecto usa EXACTAMENTE los mismos patrones documentados arriba. NO inventar funciones adicionales.
