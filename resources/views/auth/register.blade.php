<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro — ISTPET Bar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@500;700;800;900&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body style="background:#080d1e;min-height:100vh;overflow-x:hidden;">

{{-- Fondo igual al login --}}
<div style="position:fixed;inset:0;z-index:0;pointer-events:none;">
    <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(201,168,76,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(201,168,76,0.03) 1px,transparent 1px);background-size:48px 48px;"></div>
    <div style="position:absolute;width:700px;height:700px;border-radius:50%;filter:blur(120px);background:radial-gradient(circle,rgba(27,42,107,0.35),transparent 70%);top:-200px;left:-200px;"></div>
    <div style="position:absolute;width:500px;height:500px;border-radius:50%;filter:blur(100px);background:radial-gradient(circle,rgba(201,168,76,0.08),transparent 70%);bottom:-100px;right:-100px;"></div>
    <div style="position:absolute;inset:0;background:radial-gradient(ellipse at center,transparent 30%,rgba(5,8,18,0.6) 100%);"></div>
</div>

<div style="position:relative;z-index:1;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem 1rem;">
<div style="width:100%;max-width:560px;">

    {{-- Header --}}
    <div style="text-align:center;margin-bottom:2rem;">
        <div style="display:inline-flex;align-items:center;gap:0.75rem;padding:0.5rem 1.25rem;border-radius:999px;background:rgba(201,168,76,0.08);border:1px solid rgba(201,168,76,0.2);margin-bottom:1.5rem;">
            <span style="font-family:'Inter Tight',sans-serif;font-weight:900;font-size:1rem;color:#c9a84c;">IST</span><span style="font-family:'Inter Tight',sans-serif;font-weight:900;font-size:1rem;color:white; margin-left: -4px;">PET</span>
            <span style="font-size:0.65rem;color:rgba(255,255,255,0.35);font-weight:600;text-transform:uppercase;letter-spacing:0.1em;">Bar</span>
        </div>
        <h1 style="font-family:'Inter Tight',sans-serif;font-weight:900;font-size:1.75rem;color:white;text-transform:uppercase;letter-spacing:0.04em;margin:0 0 0.4rem;">
            Crear cuenta
        </h1>
        <p style="color:rgba(255,255,255,0.38);font-size:0.875rem;">
            Regístrate para acceder al módulo de fiados y beneficios
        </p>
    </div>

    {{-- Card --}}
    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:1.5rem;padding:2rem;backdrop-filter:blur(12px);">

        {{-- Errores --}}
        @if($errors->any())
        <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);border-radius:0.875rem;padding:0.875rem 1rem;margin-bottom:1.5rem;">
            <div style="font-family:'Inter Tight',sans-serif;font-weight:800;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.1em;color:#fca5a5;margin-bottom:0.4rem;display:flex;align-items:center;gap:0.4rem;">
                <x-admin.icon name="alert" class="w-4 h-4 text-rose-400 flex-shrink-0" />
                <span>Corrige los siguientes errores</span>
            </div>
            @foreach($errors->all() as $error)
            <div style="font-size:0.8rem;color:rgba(252,165,165,0.8);padding:0.15rem 0;">• {{ $error }}</div>
            @endforeach
        </div>
        @endif

        @if(session('success'))
        <div style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);border-radius:0.875rem;padding:0.875rem 1rem;margin-bottom:1.5rem;color:#6ee7b7;font-size:0.85rem;display:flex;align-items:center;gap:0.4rem;">
            <x-admin.icon name="check-circle" class="w-4 h-4 text-emerald-400 flex-shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}" id="reg-form">
            @csrf

            {{-- Pasos visuales --}}
            <div style="display:flex;align-items:center;gap:0;margin-bottom:2rem;" id="steps-bar">
                @foreach([['1','Datos'],['2','Institución'],['3','Acceso']] as $i => $s)
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:0.3rem;" id="step-indicator-{{ $s[0] }}">
                    <div style="width:2rem;height:2rem;border-radius:50%;display:flex;align-items:center;justify-content:center;
                                font-family:'Inter Tight',sans-serif;font-weight:900;font-size:0.75rem;
                                transition:all 0.3s ease;
                                {{ $i === 0 ? 'background:linear-gradient(135deg,#a07d2e,#c9a84c);color:#0b1133;box-shadow:0 0 16px rgba(201,168,76,0.4);' : 'background:rgba(255,255,255,0.06);color:rgba(255,255,255,0.3);border:1px solid rgba(255,255,255,0.1);' }}"
                         id="step-circle-{{ $s[0] }}">{{ $s[0] }}</div>
                    <span style="font-size:0.6rem;font-family:'Inter Tight',sans-serif;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;
                                 {{ $i === 0 ? 'color:#c9a84c;' : 'color:rgba(255,255,255,0.25);' }}"
                          id="step-label-{{ $s[0] }}">{{ $s[1] }}</span>
                </div>
                @if($i < 2)
                <div style="flex:1;height:1px;background:rgba(255,255,255,0.1);margin-bottom:1rem;" id="step-line-{{ $i+1 }}"></div>
                @endif
                @endforeach
            </div>

            {{-- PASO 1: Datos personales --}}
            <div id="paso-1">
                <div style="margin-bottom:1rem;">
                    <label style="display:block;font-family:'Inter Tight',sans-serif;font-weight:700;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.12em;color:rgba(255,255,255,0.4);margin-bottom:0.5rem;">
                        Nombre completo *
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="Tu nombre y apellido"
                           pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]{3,100}$"
                           title="Solo letras y espacios (mínimo 3 caracteres)"
                           style="width:100%;padding:0.75rem 1rem;border-radius:0.75rem;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.05);color:white;font-size:0.9rem;outline:none;box-sizing:border-box;transition:border-color 0.2s;"
                           onfocus="this.style.borderColor='rgba(201,168,76,0.5)'"
                           onblur="this.style.borderColor='rgba(255,255,255,0.1)'"
                           onkeypress="return /[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]/.test(event.key)"
                           required>
                    @error('name')<span style="font-size:0.72rem;color:#f87171;margin-top:0.25rem;display:block;">{{ $message }}</span>@enderror
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:1.5rem;">
                    <div>
                        <label style="display:block;font-family:'Inter Tight',sans-serif;font-weight:700;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.12em;color:rgba(255,255,255,0.4);margin-bottom:0.5rem;">
                            Cédula *
                        </label>
                        <div style="position:relative;">
                            <input type="text" name="cedula" id="input-cedula" value="{{ old('cedula') }}"
                                   placeholder="0912345678" maxlength="10" inputmode="numeric" pattern="[0-9]{10}"
                                   title="10 dígitos numéricos"
                                   style="width:100%;padding:0.75rem 2.5rem 0.75rem 1rem;border-radius:0.75rem;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.05);color:white;font-size:0.9rem;outline:none;box-sizing:border-box;font-family:monospace;transition:border-color 0.2s;"
                                   onfocus="this.style.borderColor='rgba(201,168,76,0.5)'"
                                   onblur="this.style.borderColor='rgba(255,255,255,0.1)';if(validarCedulaJS(this.value)){buscarNombrePorCedula(this.value);verificarCedulaUnica(this.value);}"
                                   onkeypress="return /[0-9]/.test(event.key)"
                                   oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)"
                                   required>
                            <span id="cedula-icon" style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);display:flex;align-items:center;"></span>
                        </div>
                        <span id="cedula-msg" style="font-size:0.72rem;margin-top:0.25rem;display:block;"></span>
                        @error('cedula')<span style="font-size:0.72rem;color:#f87171;margin-top:0.25rem;display:block;">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label style="display:block;font-family:'Inter Tight',sans-serif;font-weight:700;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.12em;color:rgba(255,255,255,0.4);margin-bottom:0.5rem;">
                            Teléfono *
                        </label>
                        <input type="tel" name="telefono" value="{{ old('telefono') }}"
                               placeholder="0991234567" maxlength="10" id="input-telefono"
                               inputmode="numeric" pattern="09[0-9]{8}" title="Celular de 10 dígitos que comience con 09"
                               style="width:100%;padding:0.75rem 1rem;border-radius:0.75rem;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.05);color:white;font-size:0.9rem;outline:none;box-sizing:border-box;font-family:monospace;transition:border-color 0.2s;"
                               onfocus="this.style.borderColor='rgba(201,168,76,0.5)'"
                               onblur="this.style.borderColor='rgba(255,255,255,0.1)';validarTelefono(this.value);if(this.value.length===10)verificarTelefonoUnico(this.value)"
                               onkeypress="return /[0-9]/.test(event.key)"
                               oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)"
                               required>
                        <span id="tel-msg" style="font-size:0.72rem;margin-top:0.25rem;display:block;"></span>
                        @error('telefono')<span style="font-size:0.72rem;color:#f87171;margin-top:0.25rem;display:block;">{{ $message }}</span>@enderror
                    </div>
                </div>

                <button type="button" onclick="irPaso(2)"
                        style="width:100%;padding:0.9rem;border-radius:0.875rem;border:none;cursor:pointer;
                               background:linear-gradient(135deg,#a07d2e,#c9a84c,#e2c47a);
                               color:#0b1133;font-family:'Inter Tight',sans-serif;font-weight:900;
                               font-size:0.875rem;text-transform:uppercase;letter-spacing:0.06em;
                               box-shadow:0 6px 24px rgba(201,168,76,0.3);transition:all 0.2s;"
                        onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 10px 30px rgba(201,168,76,0.4)'"
                        onmouseout="this.style.transform='';this.style.boxShadow='0 6px 24px rgba(201,168,76,0.3)'">
                    Siguiente →
                </button>
            </div>

            {{-- PASO 2: Institución y carrera --}}
            <div id="paso-2" style="display:none;">
                <div style="margin-bottom:1.25rem;">
                    <label style="display:block;font-family:'Inter Tight',sans-serif;font-weight:700;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.12em;color:rgba(255,255,255,0.4);margin-bottom:0.5rem;">
                        ¿A qué institución perteneces? *
                    </label>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;" id="sede-selector">
                        <label style="cursor:pointer;" id="card-instituto">
                            <input type="radio" name="tipo_sede" value="instituto" class="hidden" {{ old('tipo_sede') === 'instituto' ? 'checked' : '' }} onchange="onSedeChange('instituto')">
                            <div style="padding:1rem;border-radius:0.875rem;border:2px solid rgba(255,255,255,0.08);background:rgba(255,255,255,0.03);text-align:center;transition:all 0.2s;" id="sede-card-instituto">
                                <div style="margin-bottom:0.4rem;display:flex;justify-content:center;color:#c9a84c;">
                                    <x-admin.icon name="instituto" class="w-8 h-8" />
                                </div>
                                <div style="font-family:'Inter Tight',sans-serif;font-weight:900;font-size:0.75rem;color:white;text-transform:uppercase;letter-spacing:0.06em;">Instituto</div>
                                <div style="font-size:0.65rem;color:rgba(255,255,255,0.35);margin-top:0.2rem;">Tecnológico</div>
                            </div>
                        </label>
                        <label style="cursor:pointer;" id="card-conduccion">
                            <input type="radio" name="tipo_sede" value="conduccion" class="hidden" {{ old('tipo_sede') === 'conduccion' ? 'checked' : '' }} onchange="onSedeChange('conduccion')">
                            <div style="padding:1rem;border-radius:0.875rem;border:2px solid rgba(255,255,255,0.08);background:rgba(255,255,255,0.03);text-align:center;transition:all 0.2s;" id="sede-card-conduccion">
                                <div style="margin-bottom:0.4rem;display:flex;justify-content:center;color:#60a5fa;">
                                    <x-admin.icon name="car" class="w-8 h-8" />
                                </div>
                                <div style="font-family:'Inter Tight',sans-serif;font-weight:900;font-size:0.75rem;color:white;text-transform:uppercase;letter-spacing:0.06em;">Conducción</div>
                                <div style="font-size:0.65rem;color:rgba(255,255,255,0.35);margin-top:0.2rem;">Escuela</div>
                            </div>
                        </label>
                    </div>
                    @error('tipo_sede')<span style="font-size:0.72rem;color:#f87171;margin-top:0.25rem;display:block;">{{ $message }}</span>@enderror
                </div>

                {{-- Carrera instituto --}}
                <div id="carreras-instituto" style="display:{{ old('tipo_sede') === 'instituto' ? 'block' : 'none' }};margin-bottom:1.25rem;">
                    <label style="display:block;font-family:'Inter Tight',sans-serif;font-weight:700;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.12em;color:rgba(255,255,255,0.4);margin-bottom:0.5rem;">
                        Carrera *
                    </label>
                    <select name="carrera" id="select-carrera-instituto"
                            style="width:100%;padding:0.75rem 1rem;border-radius:0.75rem;border:1px solid rgba(255,255,255,0.1);background:#1a1f32;color:white;font-size:0.875rem;outline:none;transition:border-color 0.2s;"
                            onfocus="this.style.borderColor='rgba(201,168,76,0.5)'"
                            onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                        <option value="">— Selecciona tu carrera —</option>
                        <optgroup label="Presencial">
                            <option value="Desarrollo de Software - Presencial" {{ old('carrera') === 'Desarrollo de Software - Presencial' ? 'selected' : '' }}>Desarrollo de Software</option>
                            <option value="Diseño Gráfico - Presencial" {{ old('carrera') === 'Diseño Gráfico - Presencial' ? 'selected' : '' }}>Diseño Gráfico</option>
                            <option value="Entrenamiento Deportivo - Presencial" {{ old('carrera') === 'Entrenamiento Deportivo - Presencial' ? 'selected' : '' }}>Entrenamiento Deportivo</option>
                            <option value="Educación Inicial - Presencial" {{ old('carrera') === 'Educación Inicial - Presencial' ? 'selected' : '' }}>Educación Inicial</option>
                            <option value="Mecánica Automotriz - Presencial" {{ old('carrera') === 'Mecánica Automotriz - Presencial' ? 'selected' : '' }}>Mecánica Automotriz</option>
                        </optgroup>
                        <optgroup label="Semipresencial">
                            <option value="Educación Básica - Semipresencial" {{ old('carrera') === 'Educación Básica - Semipresencial' ? 'selected' : '' }}>Educación Básica</option>
                            <option value="Electrónica - Semipresencial" {{ old('carrera') === 'Electrónica - Semipresencial' ? 'selected' : '' }}>Electrónica</option>
                            <option value="Gastronomía - Semipresencial" {{ old('carrera') === 'Gastronomía - Semipresencial' ? 'selected' : '' }}>Gastronomía</option>
                            <option value="Redes y Telecomunicaciones - Semipresencial" {{ old('carrera') === 'Redes y Telecomunicaciones - Semipresencial' ? 'selected' : '' }}>Redes & Telecomunicaciones</option>
                        </optgroup>
                        <optgroup label="En Línea">
                            <option value="Desarrollo de Software - En Línea" {{ old('carrera') === 'Desarrollo de Software - En Línea' ? 'selected' : '' }}>Desarrollo de Software</option>
                            <option value="Contabilidad y Asesoría Tributaria - En Línea" {{ old('carrera') === 'Contabilidad y Asesoría Tributaria - En Línea' ? 'selected' : '' }}>Contabilidad y Asesoría Tributaria</option>
                            <option value="Educación Inclusiva - En Línea" {{ old('carrera') === 'Educación Inclusiva - En Línea' ? 'selected' : '' }}>Educación Inclusiva</option>
                            <option value="Marketing y Comercio Electrónico - En Línea" {{ old('carrera') === 'Marketing y Comercio Electrónico - En Línea' ? 'selected' : '' }}>Marketing & Comercio Electrónico</option>
                        </optgroup>
                        <optgroup label="Híbrida">
                            <option value="Talento Humano - Híbrida" {{ old('carrera') === 'Talento Humano - Híbrida' ? 'selected' : '' }}>Talento Humano</option>
                        </optgroup>
                    </select>
                </div>

                {{-- Carrera conducción --}}
                <div id="carreras-conduccion" style="display:{{ old('tipo_sede') === 'conduccion' ? 'block' : 'none' }};margin-bottom:1.25rem;">
                    <label style="display:block;font-family:'Inter Tight',sans-serif;font-weight:700;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.12em;color:rgba(255,255,255,0.4);margin-bottom:0.5rem;">
                        Programa *
                    </label>
                    <select name="carrera" id="select-carrera-conduccion"
                            style="width:100%;padding:0.75rem 1rem;border-radius:0.75rem;border:1px solid rgba(255,255,255,0.1);background:#1a1f32;color:white;font-size:0.875rem;outline:none;transition:border-color 0.2s;"
                            onfocus="this.style.borderColor='rgba(201,168,76,0.5)'"
                            onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
                        <option value="">— Selecciona tu programa —</option>
                        <option value="Conducción - Licencia Tipo B" {{ old('carrera') === 'Conducción - Licencia Tipo B' ? 'selected' : '' }}>Licencia Tipo B</option>
                        <option value="Conducción - Licencia Tipo C" {{ old('carrera') === 'Conducción - Licencia Tipo C' ? 'selected' : '' }}>Licencia Tipo C</option>
                        <option value="Conducción - Licencia Tipo A" {{ old('carrera') === 'Conducción - Licencia Tipo A' ? 'selected' : '' }}>Licencia Tipo A</option>
                        <option value="Conducción - Renovación" {{ old('carrera') === 'Conducción - Renovación' ? 'selected' : '' }}>Renovación de Licencia</option>
                    </select>
                </div>

                @error('carrera')<span style="font-size:0.72rem;color:#f87171;margin-bottom:0.75rem;display:block;">{{ $message }}</span>@enderror

                <div style="display:flex;gap:0.75rem;">
                    <button type="button" onclick="irPaso(1)"
                            style="flex:1;padding:0.9rem;border-radius:0.875rem;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.6);font-family:'Inter Tight',sans-serif;font-weight:700;font-size:0.875rem;text-transform:uppercase;letter-spacing:0.06em;cursor:pointer;transition:all 0.2s;"
                            onmouseover="this.style.background='rgba(255,255,255,0.1)'"
                            onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                        ← Atrás
                    </button>
                    <button type="button" onclick="irPaso(3)"
                            style="flex:2;padding:0.9rem;border-radius:0.875rem;border:none;cursor:pointer;
                                   background:linear-gradient(135deg,#a07d2e,#c9a84c,#e2c47a);
                                   color:#0b1133;font-family:'Inter Tight',sans-serif;font-weight:900;
                                   font-size:0.875rem;text-transform:uppercase;letter-spacing:0.06em;
                                   box-shadow:0 6px 24px rgba(201,168,76,0.3);transition:all 0.2s;"
                            onmouseover="this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.transform=''">
                        Siguiente →
                    </button>
                </div>
            </div>

            {{-- PASO 3: Acceso (email + password) --}}
            <div id="paso-3" style="display:none;">
                <div style="margin-bottom:1.25rem;">
                    <label style="display:block;font-family:'Inter Tight',sans-serif;font-weight:700;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.12em;color:rgba(255,255,255,0.4);margin-bottom:0.5rem;">
                        Correo electrónico *
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="usuario@ejemplo.com"
                           style="width:100%;padding:0.75rem 1rem;border-radius:0.75rem;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.05);color:white;font-size:0.9rem;outline:none;box-sizing:border-box;transition:border-color 0.2s;"
                           onfocus="this.style.borderColor='rgba(201,168,76,0.5)'"
                           onblur="this.style.borderColor='rgba(255,255,255,0.1)'"
                           required>
                    @error('email')<span style="font-size:0.72rem;color:#f87171;margin-top:0.25rem;display:block;">{{ $message }}</span>@enderror
                </div>

                <div style="margin-bottom:1.25rem;">
                    <label style="display:block;font-family:'Inter Tight',sans-serif;font-weight:700;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.12em;color:rgba(255,255,255,0.4);margin-bottom:0.5rem;">
                        Contraseña * <span style="font-weight:500;text-transform:none;letter-spacing:0;color:rgba(255,255,255,0.2);">(mínimo 8 caracteres)</span>
                    </label>
                    <input type="password" name="password" id="inp-pwd"
                           placeholder="••••••••"
                           style="width:100%;padding:0.75rem 1rem;border-radius:0.75rem;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.05);color:white;font-size:0.9rem;outline:none;box-sizing:border-box;transition:border-color 0.2s;"
                           onfocus="this.style.borderColor='rgba(201,168,76,0.5)'"
                           onblur="this.style.borderColor='rgba(255,255,255,0.1)'"
                           oninput="checkPwd(this.value)"
                           required>
                    <div id="pwd-strength" style="height:3px;border-radius:999px;background:rgba(255,255,255,0.08);margin-top:0.4rem;overflow:hidden;">
                        <div id="pwd-bar" style="height:100%;width:0%;border-radius:999px;transition:all 0.3s ease;"></div>
                    </div>
                    @error('password')<span style="font-size:0.72rem;color:#f87171;margin-top:0.25rem;display:block;">{{ $message }}</span>@enderror
                </div>

                <div style="margin-bottom:1.5rem;">
                    <label style="display:block;font-family:'Inter Tight',sans-serif;font-weight:700;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.12em;color:rgba(255,255,255,0.4);margin-bottom:0.5rem;">
                        Confirmar contraseña *
                    </label>
                    <input type="password" name="password_confirmation"
                           placeholder="••••••••"
                           style="width:100%;padding:0.75rem 1rem;border-radius:0.75rem;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.05);color:white;font-size:0.9rem;outline:none;box-sizing:border-box;transition:border-color 0.2s;"
                           onfocus="this.style.borderColor='rgba(201,168,76,0.5)'"
                           onblur="this.style.borderColor='rgba(255,255,255,0.1)'"
                           required>
                </div>

                <div style="display:flex;gap:0.75rem;">
                    <button type="button" onclick="irPaso(2)"
                            style="flex:1;padding:0.9rem;border-radius:0.875rem;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.6);font-family:'Inter Tight',sans-serif;font-weight:700;font-size:0.875rem;text-transform:uppercase;letter-spacing:0.06em;cursor:pointer;transition:all 0.2s;"
                            onmouseover="this.style.background='rgba(255,255,255,0.1)'"
                            onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                        ← Atrás
                    </button>
                    <button type="submit" id="btn-registrar"
                            style="flex:2;padding:0.9rem;border-radius:0.875rem;border:none;cursor:pointer;
                                   background:linear-gradient(135deg,#a07d2e,#c9a84c,#e2c47a);
                                   color:#0b1133;font-family:'Inter Tight',sans-serif;font-weight:900;
                                   font-size:0.875rem;text-transform:uppercase;letter-spacing:0.06em;
                                   box-shadow:0 6px 24px rgba(201,168,76,0.3);transition:all 0.2s;display:flex;align-items:center;justify-content:center;gap:0.4rem;"
                            onmouseover="this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.transform=''">
                        <x-admin.icon name="check" class="w-4 h-4 text-gray-950" />
                        <span>Crear cuenta</span>
                    </button>
                </div>
            </div>

        </form>
    </div>

    {{-- Footer --}}
    <div style="text-align:center;margin-top:1.5rem;">
        <span style="color:rgba(255,255,255,0.3);font-size:0.85rem;">¿Ya tienes una cuenta?</span>
        <a href="{{ route('login') }}"
           style="color:#c9a84c;font-weight:700;font-size:0.85rem;text-decoration:none;margin-left:0.4rem;transition:color 0.2s;"
           onmouseover="this.style.color='#e2c47a'"
           onmouseout="this.style.color='#c9a84c'">
            Iniciar sesión →
        </a>
    </div>

</div>
</div>

<script>
let pasoActual = 1;

// Si hay errores de servidor, mostrar el paso correcto
@if($errors->has('cedula') || $errors->has('name') || $errors->has('telefono'))
    pasoActual = 1;
@elseif($errors->has('tipo_sede') || $errors->has('carrera'))
    pasoActual = 2;
@elseif($errors->has('email') || $errors->has('password'))
    pasoActual = 3;
@endif

document.addEventListener('DOMContentLoaded', () => {
    mostrarPaso(pasoActual);
    // Restaurar sede seleccionada
    const sedePrev = '{{ old('tipo_sede') }}';
    if (sedePrev) onSedeChange(sedePrev);
});

function mostrarPaso(paso) {
    [1,2,3].forEach(p => {
        const el = document.getElementById('paso-' + p);
        if (el) el.style.display = p === paso ? 'block' : 'none';

        const circle = document.getElementById('step-circle-' + p);
        const label  = document.getElementById('step-label-' + p);
        if (circle && label) {
            if (p < paso) {
                circle.style.background = 'linear-gradient(135deg,#166534,#22c55e)';
                circle.style.color = 'white';
                circle.style.boxShadow = '0 0 12px rgba(34,197,94,0.3)';
                circle.textContent = '✓';
                label.style.color = '#6ee7b7';
            } else if (p === paso) {
                circle.style.background = 'linear-gradient(135deg,#a07d2e,#c9a84c)';
                circle.style.color = '#0b1133';
                circle.style.boxShadow = '0 0 16px rgba(201,168,76,0.4)';
                circle.textContent = p;
                label.style.color = '#c9a84c';
            } else {
                circle.style.background = 'rgba(255,255,255,0.06)';
                circle.style.color = 'rgba(255,255,255,0.3)';
                circle.style.boxShadow = 'none';
                circle.textContent = p;
                label.style.color = 'rgba(255,255,255,0.25)';
            }
        }
    });
    pasoActual = paso;
}

function irPaso(paso) {
    if (paso > pasoActual && !validarPaso(pasoActual)) return;
    mostrarPaso(paso);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function validarPaso(paso) {
    if (paso === 1) {
        const name   = document.querySelector('[name=name]').value.trim();
        const cedula = document.querySelector('[name=cedula]').value.trim();
        const tel    = document.querySelector('[name=telefono]').value.trim();
        if (!name)   { alert('Ingresa tu nombre completo'); return false; }
        if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/.test(name)) { alert('El nombre solo puede contener letras'); return false; }
        if (name.trim().split(/\s+/).length < 2) { alert('Ingresa al menos nombre y apellido'); return false; }
        if (!cedula || cedula.length !== 10) { alert('Ingresa una cédula válida de 10 dígitos'); return false; }
        if (!validarCedulaJS(cedula)) return false;
        if (!tel)    { alert('Ingresa tu teléfono'); return false; }
        if (tel.length !== 10 || !tel.startsWith('09')) { alert('El teléfono debe ser un celular ecuatoriano de 10 dígitos que inicie con 09'); return false; }
        return true;
    }
    if (paso === 2) {
        const sede = document.querySelector('[name=tipo_sede]:checked');
        if (!sede)   { alert('Selecciona tu institución'); return false; }
        const carrera = sede.value === 'instituto'
            ? document.getElementById('select-carrera-instituto').value
            : document.getElementById('select-carrera-conduccion').value;
        // Sync the right select name
        if (sede.value === 'instituto') {
            document.getElementById('select-carrera-instituto').name = 'carrera';
            document.getElementById('select-carrera-conduccion').name = '';
        } else {
            document.getElementById('select-carrera-conduccion').name = 'carrera';
            document.getElementById('select-carrera-instituto').name = '';
        }
        if (!carrera) { alert('Selecciona tu carrera'); return false; }
        return true;
    }
    return true;
}

function onSedeChange(tipo) {
    const ci = document.getElementById('sede-card-instituto');
    const cc = document.getElementById('sede-card-conduccion');
    if (ci) {
        ci.style.borderColor = tipo === 'instituto' ? '#c9a84c' : 'rgba(255,255,255,0.08)';
        ci.style.background  = tipo === 'instituto' ? 'rgba(201,168,76,0.07)' : 'rgba(255,255,255,0.03)';
    }
    if (cc) {
        cc.style.borderColor = tipo === 'conduccion' ? '#c9a84c' : 'rgba(255,255,255,0.08)';
        cc.style.background  = tipo === 'conduccion' ? 'rgba(201,168,76,0.07)' : 'rgba(255,255,255,0.03)';
    }
    document.getElementById('carreras-instituto').style.display  = tipo === 'instituto'  ? 'block' : 'none';
    document.getElementById('carreras-conduccion').style.display = tipo === 'conduccion' ? 'block' : 'none';
}

// Auto-completar nombre desde cédula (API pública Ecuador)
async function buscarNombrePorCedula(cedula) {
    if (cedula.length !== 10) return;
    if (!validarCedulaJS(cedula)) return;

    const icon = document.getElementById('cedula-icon');
    const msg  = document.getElementById('cedula-msg');
    if (icon) icon.innerHTML = '<svg class="w-4 h-4 text-amber-400 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
    if (msg)  { msg.textContent = 'Buscando...'; msg.style.color = 'rgba(255,255,255,0.4)'; }

    try {
        // API pública del SRI Ecuador
        const res  = await fetch(`https://srienlinea.sri.gob.ec/sri-catastro-sujeto-servicio-internet/rest/Persona/obtenerPersona?numeroRuc=${cedula}`, {
            signal: AbortSignal.timeout(5000)
        });
        if (!res.ok) throw new Error('no response');
        const data = await res.json();

        if (data && (data.nombreCompleto || (data.nombre && data.apellido))) {
            const nombre = data.nombreCompleto
                || `${data.nombre || ''} ${data.apellido || ''}`.trim();

            const campoNombre = document.querySelector('[name=name]');
            if (campoNombre && nombre && campoNombre.value.trim() === '') {
                // Convertir a Title Case
                campoNombre.value = nombre.toLowerCase().replace(/\b\w/g, c => c.toUpperCase());
                if (msg) { msg.textContent = 'Cédula válida · Nombre autocompletado'; msg.style.color = '#6ee7b7'; }
            } else {
                if (msg) { msg.textContent = 'Cédula válida'; msg.style.color = '#6ee7b7'; }
            }
        } else {
            if (msg) { msg.textContent = 'Cédula válida'; msg.style.color = '#6ee7b7'; }
        }
    } catch(e) {
        // Si la API falla, no pasa nada — validación local ya se hizo
        if (msg && msg.textContent === 'Buscando...') {
            msg.textContent = 'Cédula válida';
            msg.style.color = '#6ee7b7';
        }
    }
}

// Validar teléfono — exactamente 10 dígitos iniciando con 09
function validarTelefono(val) {
    const msg = document.getElementById('tel-msg');
    if (!msg) return;
    if (!val || val.length !== 10 || !val.startsWith('09')) {
        msg.textContent = 'Debe tener 10 dígitos y comenzar con 09';
        msg.style.color = '#f87171';
        return false;
    }
    msg.textContent = 'Teléfono válido';
    msg.style.color = '#6ee7b7';
    return true;
}

// Validación cédula ecuatoriana en JS
function validarCedulaJS(cedula) {
    const msg  = document.getElementById('cedula-msg');
    const icon = document.getElementById('cedula-icon');
    if (!cedula || cedula.length !== 10) return true; // no mostrar error hasta blur completo
    const digitos = cedula.split('').map(Number);
    const prov = parseInt(cedula.substring(0,2));
    if (prov < 1 || prov > 24) {
        if (msg)  { msg.textContent = 'Provincia inválida'; msg.style.color = '#f87171'; }
        if (icon) icon.innerHTML = '<svg class="w-4 h-4 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
        return false;
    }
    let suma = 0;
    for (let i = 0; i < 9; i++) {
        let v = digitos[i];
        if (i % 2 === 0) { v *= 2; if (v > 9) v -= 9; }
        suma += v;
    }
    const residuo = suma % 10;
    const calc = residuo === 0 ? 0 : 10 - residuo;
    if (calc !== digitos[9]) {
        if (msg)  { msg.textContent = 'Cédula inválida'; msg.style.color = '#f87171'; }
        if (icon) icon.innerHTML = '<svg class="w-4 h-4 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
        return false;
    }
    if (msg)  { msg.textContent = 'Cédula válida'; msg.style.color = '#6ee7b7'; }
    if (icon) icon.innerHTML = '<svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>';
    return true;
}

function checkPwd(val) {
    const bar = document.getElementById('pwd-bar');
    if (!bar) return;
    const len = val.length;
    let pct = 0, color = '#ef4444';
    if (len >= 8)  { pct = 40; color = '#f97316'; }
    if (len >= 10) { pct = 65; color = '#eab308'; }
    if (len >= 12 && /[A-Z]/.test(val) && /[0-9]/.test(val)) { pct = 100; color = '#22c55e'; }
    else if (len >= 10 && /[0-9]/.test(val)) { pct = Math.max(pct, 80); }
    bar.style.width = pct + '%';
    bar.style.background = color;
}

// Verificar si cédula ya está registrada
async function verificarCedulaUnica(cedula) {
    if (cedula.length !== 10) return;
    const msg  = document.getElementById('cedula-msg');
    const icon = document.getElementById('cedula-icon');
    const btn  = document.querySelector('#paso-1 button[type=button]');
    try {
        const res  = await fetch(`/registro/verificar-cedula?cedula=${cedula}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.existe) {
            if (msg)  { msg.textContent = 'Esta cédula ya tiene una cuenta registrada'; msg.style.color = '#f87171'; }
            if (icon) icon.innerHTML = '<svg class="w-4 h-4 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
            if (btn)  { btn.disabled = true; btn.style.opacity = '0.4'; btn.style.cursor = 'not-allowed'; btn.title = 'Cédula ya registrada'; }
        } else {
            if (btn)  { btn.disabled = false; btn.style.opacity = '1'; btn.style.cursor = 'pointer'; btn.title = ''; }
        }
    } catch(e) {
        // Si falla la verificación, no bloqueamos
    }
}

async function verificarTelefonoUnico(telefono) {
    if (telefono.length !== 10) return;
    const msg = document.getElementById('tel-msg');
    const btn = document.querySelector('#paso-1 button[type=button]');
    try {
        const res  = await fetch(`/registro/verificar-telefono?telefono=${telefono}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.existe) {
            if (msg) { msg.textContent = 'Este teléfono ya tiene una cuenta registrada'; msg.style.color = '#f87171'; }
            if (btn) { btn.disabled = true; btn.style.opacity = '0.4'; btn.style.cursor = 'not-allowed'; btn.title = 'Teléfono ya registrado'; }
        } else {
            if (btn) { btn.disabled = false; btn.style.opacity = '1'; btn.style.cursor = 'pointer'; btn.title = ''; }
        }
    } catch(e) {}
}
</script>

</body>
</html>