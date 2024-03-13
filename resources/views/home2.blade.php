@extends('layouts.admin')
@section('content')
    <div class="content-wrapper">
        <div class="container-xxl">
            <div class="card">
                <div class="control-part1">
                    <button class="btn btn-danger" id="prevBtn">&lt;</button>
                    <h2 id="monthYear"></h2>
                    <button class="btn btn-danger" id="nextBtn">&gt;</button>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered monthly-table" id="calendar">
                        <thead>
                            <tr></tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let currentDate = new Date();

            // Function to generate the calendar
            function renderCalendar() {
                const thead = $("#calendar thead tr");
                const tbody = $("#calendar tbody");
                thead.html("");
                tbody.html("");
                const daysInMonth = new Date(
                    currentDate.getFullYear(),
                    currentDate.getMonth() + 1,
                    0
                ).getDate();
                const japaneseMonths = [
                    "1月", "2月", "3月", "4月", "5月", "6月",
                    "7月", "8月", "9月", "10月", "11月", "12月"
                ];
                $("#monthYear").text(
                    `${japaneseMonths[currentDate.getMonth()]} ${currentDate.getFullYear()}`
                );

                // Generate calendar header
                const japaneseDayNames = ["日", "月", "火", "水", "木", "金", "土"];
                for (let day = 0; day <= daysInMonth; day++) {
                    const currentDateForDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), day);
                    const dayOfWeekIndex = currentDateForDay.getDay(); // Get the day of the week index (0-6)
                    const dayOfWeekJapanese = japaneseDayNames[
                        dayOfWeekIndex]; // Get the corresponding Japanese day name
                    const thText = day === 0 ? "学年" : `<div>${day}</div> <div>${dayOfWeekJapanese}</div>`;
                    const th = $("<th>").html(`${thText}`);
                    thead.append(th);
                }

                // Generate calendar body
                const grades = ['1年', '2年', '3年', '4年', '5年', '6年', '合計'];
                for (let i = 0; i < 7; i++) {
                    const row = $("<tr>");
                    const gradeCell = $("<td>").text(grades[i]);
                    row.append(gradeCell);
                    for (let j = 1; j <= daysInMonth; j++) {
                        const cell = $("<td>");
                        if(i !== 0) {
                           const formattedDate =
                               `${currentDate.getFullYear()}-${('0' + (currentDate.getMonth() + 1)).slice(-2)}-${('0' + j).slice(-2)}`;
                           console.log(formattedDate)
   
                           row.append(cell);
   
                           $.post("{{ route('home.get') }}", {
                               "_token": $('meta[name="csrf_token"]').attr('content'),
                               "grade" : i,
                               "date" : formattedDate
                           }, function(data) {
                               var resp = data.info;
                               if (data.status == 200) {
                                   toastr.success(data.message);
   
                               } else if (data.status == 401) {
                                   toastr.error(data.message);
                               }
                           }, 'json').catch((error) => {
                               toastr.error("エラーが発生しました。");
                           });
                        }
                        
                    }
                    tbody.append(row);
                }
            }

            // Initial calendar display

            // Prev
            function prevMonth() {
                currentDate.setMonth(currentDate.getMonth() - 1);
                renderCalendar();
            }

            // Next
            function nextMonth() {
                currentDate.setMonth(currentDate.getMonth() + 1);
                renderCalendar();
            }

            $("#prevBtn").on("click", prevMonth);
            $("#nextBtn").on("click", nextMonth);
            renderCalendar();
        });
    </script>
@endsection