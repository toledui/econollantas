<!DOCTYPE html>
<html lang="es">

<head>
 <meta charset="UTF-8">
 <title>Certificado de Finalización</title>
 <style>
 @page {
 margin: 0px;
 /* Cero márgenes físicos */
 size: landscape;
 }

 body {
 margin: 0px;
 padding: 0px;
 font-family:'Helvetica','Arial', sans-serif;
 background-color: #fff;
 }

 /* 1. Usar FIXEd para la marca de agua. Así no ocupa espacio en el flujo del HTML */
 .watermark {
 position: fixed;
 top: 0px;
 left: 0px;
 right: 0px;
 bottom: 0px;
 background-image: url('{{ $watermark }}');
 background-repeat: repeat;
 background-size: 150px;
 opacity: 0.05;
 z-index: -10;
 }

 /* 2. Usar ABSOLUTE con coordenadas fijas en lugar de height: 100% */
 .border-outer {
 position: absolute;
 top: 30px;
 bottom: 30px;
 left: 30px;
 right: 30px;
 border: 12px solid
 {{ $primaryColor }}
 ;
 }

 /* 3. El borde interior también con coordenadas exactas */
 .border-inner {
 position: absolute;
 top: 8px;
 bottom: 8px;
 left: 8px;
 right: 8px;
 border: 2px solid
 {{ $primaryColor }}
 ;
 text-align: center;
 }

 /* Contenedor de contenido ajustado de forma absoluta */
 .content-wrapper {
 position: absolute;
 top: 40px;
 left: 0;
 width: 100%;
 }

 .logo {
 max-height: 70px;
 width: auto;
 margin-bottom: 5px;
 }

 .company-name {
 font-size: 14px;
 font-weight: bold;
 text-transform: uppercase;
 letter-spacing: 2px;
 color:
 {{ $primaryColor }}
 ;
 }

 .main-title {
 font-size: 40px;
 font-weight: bold;
 margin: 15px 0 5px 0;
 color: #0f172a;
 }

 .subtitle {
 font-size: 16px;
 margin-bottom: 25px;
 color: #64748b;
 letter-spacing: 1px;
 }

 .recipient-name {
 font-size: 36px;
 font-weight: bold;
 color:
 {{ $primaryColor }}
 ;
 margin: 0 auto;
 border-bottom: 2px solid #e2e8f0;
 display: inline-block;
 padding-bottom: 5px;
 min-width: 60%;
 }

 .course-text {
 font-size: 16px;
 margin-top: 20px;
 color: #64748b;
 }

 .course-name {
 font-size: 24px;
 font-weight: bold;
 color: #0f172a;
 margin: 10px 0;
 font-style: italic;
 }

 /* Footer con tabla para dompdf, pegado al fondo exacto */
 .footer-table {
 position: absolute;
 bottom: 30px;
 width: 100%;
 border-collapse: collapse;
 }

 .footer-table td {
 width: 33.3%;
 text-align: center;
 vertical-align: bottom;
 padding: 0 20px;
 }

 .signature-line {
 border-top: 1px solid #94a3b8;
 margin: 0 auto 5px;
 width: 80%;
 }

 .footer-text {
 font-size: 10px;
 color: #94a3b8;
 text-transform: uppercase;
 font-weight: bold;
 }

 .date-val {
 font-size: 13px;
 font-weight: bold;
 color: #334155;
 margin-bottom: 3px;
 }

 /* Esquinas */
 .corner {
 position: absolute;
 width: 70px;
 height: 70px;
 background-color:
 {{ $primaryColor }}
 ;
 z-index: 10;
 }

 .top-left {
 top: -12px;
 left: -12px;
 border-radius: 0 0 100% 0;
 }

 .bottom-right {
 bottom: -12px;
 right: -12px;
 border-radius: 100% 0 0 0;
 }
 </style>
</head>

<body>

 <div class="watermark"></div>

 <div class="border-outer">
 <div class="corner top-left"></div>
 <div class="corner bottom-right"></div>

 <div class="border-inner">

 <div class="content-wrapper">
 @if($logo)
 <img src="{{ $logo }}" class="logo">
 @endif
 <div class="company-name">{{ $companyName }}</div>

 <div class="main-title">CERTIFICADO</div>
 <div class="subtitle">DE FINALIZACIÓN</div>

 <div class="course-text">ESTE RECONOCIMIENTO SE OTORGA A:</div>
 <div class="recipient-name">{{ $userName }}</div>

 <div class="course-text">POR HABER COMPLETADO EL CURSO:</div>
 <div class="course-name">"{{ $courseName }}"</div>

 <div style="font-size: 12px; margin-top: 25px; color: #64748b;">
 Expedido con un desempeño sobresaliente bajo los estándares de {{ $companyName }}.
 </div>
 </div>

 <table class="footer-table">
 <tr>
 <td>
 <div class="date-val">{{ $date }}</div>
 <div class="signature-line"></div>
 <div class="footer-text">Fecha de Emisión</div>
 </td>
 <td>
 <div
 style="font-size: 16px; font-weight: bold; color: {{ $primaryColor }}; margin-bottom: 3px;">
 {{ $companyName }}
 </div>
 <div class="signature-line"></div>
 <div class="footer-text">Institución</div>
 </td>
 <td>
 <div style="height: 35px;"></div>
 <div class="signature-line"></div>
 <div class="footer-text">Firma Autorizada</div>
 </td>
 </tr>
 </table>

 </div>
 </div>

</body>

</html>