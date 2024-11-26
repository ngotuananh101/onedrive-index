<?php

return [
    'auth' => [
        'step1' => [
            'title' => 'Bước 1/3: Chuẩn bị',
            'recommendation' => 'Để có hiệu suất tốt nhất, chúng tôi khuyến nghị sử dụng REDIS làm bộ nhớ đệm. <a href="https://laravel.com/docs/11.x/redis" target="_blank" class="text-sm link">Tìm hiểu thêm</a>',
            'description' => 'Yêu cầu ủy quyền vì không có access_token hoặc refresh_token hợp lệ trên phiên bản đã triển khai này. Kiểm tra các cấu hình sau trước khi tiếp tục với tài khoản Microsoft của bạn.',
            'if_incorrect' => 'Nếu bạn thấy bất kỳ điều gì không chính xác, vui lòng thay đổi các giá trị sau trong tệp .env hoặc cấu hình onedrive và thử lại.',
            'process_to_oauth' => 'Tiến hành OAuth',
        ],
        'step2' => [
            'title' => 'Bước 2/3: Ủy quyền',
            'not_owner' => 'Nếu bạn không phải là chủ sở hữu của trang web này, hãy dừng lại ngay, vì tiếp tục quá trình này có thể làm lộ các tệp cá nhân của bạn trong OneDrive.',
            'link_created' => 'Liên kết OAuth để lấy mã ủy quyền đã được tạo. Nhấp vào nút bên dưới để lấy mã ủy quyền. Trình duyệt của bạn sẽ chuyển hướng đến trang đăng nhập tài khoản Microsoft. Sau khi đăng nhập và xác thực với tài khoản Microsoft của bạn, bạn sẽ được chuyển hướng đến bước 3 nếu thành công.',
            'authorize' => 'Ủy quyền',
        ],
        'step3' => [
            'title' => 'Bước 3/3: Thành công',
            'success' => 'Bạn đã xác thực thành công với tài khoản Microsoft của mình. Bạn có thể đóng cửa sổ này và nhấp vào nút bên dưới để chuyển hướng đến trang chủ.',
            'redirect_to_homepage' => 'Chuyển hướng đến Trang chủ',
        ],
    ],
];
