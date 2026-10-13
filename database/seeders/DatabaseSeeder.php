<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Court;
use App\Models\Equipment;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Maintenance;
use App\Models\PlayerMatch;
use App\Models\MatchMember;
use App\Models\Review;
use App\Models\EquipmentRental;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Users
        $admin = User::create([
            'name' => 'ผู้ดูแลระบบ (Admin)',
            'email' => 'admin@badminton.com',
            'password' => Hash::make('password123'),
            'phone' => '0812345678',
            'role' => 'admin',
        ]);

        $user1 = User::create([
            'name' => 'สมชาย รักแบด',
            'email' => 'somchai@gmail.com',
            'password' => Hash::make('password123'),
            'phone' => '0898765432',
            'role' => 'user',
        ]);

        $user2 = User::create([
            'name' => 'มานี มีฝีมือ',
            'email' => 'manee@gmail.com',
            'password' => Hash::make('password123'),
            'phone' => '0861112233',
            'role' => 'user',
        ]);

        // 2. Create Courts
        $court1 = Court::create([
            'name' => 'สนาม 1 (Standard Court A)',
            'floor_type' => 'ยาง PB มาตรฐาน BWF 4.5mm',
            'price_per_hour' => 150.00,
            'open_time' => '08:00:00',
            'close_time' => '22:00:00',
            'status' => 'available',
            'image' => 'courts/court_1.jpg',
            'description' => 'สนามปูพื้นยางสังเคราะห์มาตรฐานแข่งขัน แสงไฟ LED ถนอมสายตา ไร้เงาสะท้อน',
        ]);

        $court2 = Court::create([
            'name' => 'สนาม 2 (Standard Court B)',
            'floor_type' => 'ยาง PB มาตรฐาน BWF 4.5mm',
            'price_per_hour' => 150.00,
            'open_time' => '08:00:00',
            'close_time' => '22:00:00',
            'status' => 'available',
            'image' => 'courts/court_2.jpg',
            'description' => 'สนามปูพื้นยางมาตรฐาน พร้อมระบบพัดลมระบายอากาศรอบทิศทาง',
        ]);

        $court3 = Court::create([
            'name' => 'สนาม 3 (Pro Vinyl Court)',
            'floor_type' => 'ไวนิลนำเข้าซับแรงกระแทก 6.0mm',
            'price_per_hour' => 180.00,
            'open_time' => '08:00:00',
            'close_time' => '22:00:00',
            'status' => 'available',
            'image' => 'courts/court_3.jpg',
            'description' => 'พื้นหนานุ่มพิเศษ ช่วยเซฟข้อเข่าและข้อเท้า เหมาะสำหรับการฝึกซ้อมระดับมืออาชีพ',
        ]);

        $court4 = Court::create([
            'name' => 'สนาม 4 (VIP Air-Con Court)',
            'floor_type' => 'พื้นไม้ปาร์เก้สปอร์ต + พรมยาง BWF',
            'price_per_hour' => 250.00,
            'open_time' => '08:00:00',
            'close_time' => '22:00:00',
            'status' => 'available',
            'image' => 'courts/court_1.jpg',
            'description' => 'สนามปรับอากาศส่วนตัว (Air-Conditioned) พร้อมที่นั่งพักผ่อน VIP โซฟา และตู้เย็นน้ำดื่ม',
        ]);

        // 3. Create Equipments
        $eq1 = Equipment::create([
            'name' => 'ไม้แบดมินตัน Yonex Astrox Lite',
            'category' => 'ไม้แบดมินตัน',
            'rental_price' => 50.00,
            'total_stock' => 10,
            'available_stock' => 10,
            'status' => 'active',
        ]);

        $eq2 = Equipment::create([
            'name' => 'ลูกขนไก่ RSL Silver (หลอด 3 ลูก)',
            'category' => 'ลูกแบดมินตัน',
            'rental_price' => 80.00,
            'total_stock' => 20,
            'available_stock' => 20,
            'status' => 'active',
        ]);

        $eq3 = Equipment::create([
            'name' => 'รองเท้าแบดมินตัน Victor Non-Marking (ระบุไซส์ที่เคาน์เตอร์)',
            'category' => 'รองเท้า',
            'rental_price' => 60.00,
            'total_stock' => 8,
            'available_stock' => 8,
            'status' => 'active',
        ]);

        $eq4 = Equipment::create([
            'name' => 'กริปพันด้ามจับกันลื่น (Grip Tape)',
            'category' => 'อุปกรณ์เสริม',
            'rental_price' => 30.00,
            'total_stock' => 30,
            'available_stock' => 30,
            'status' => 'active',
        ]);

        // 4. Sample Maintenance (Tomorrow Court 2 08:00 - 12:00)
        Maintenance::create([
            'court_id' => $court2->id,
            'start_date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '08:00:00',
            'end_date' => Carbon::tomorrow()->toDateString(),
            'end_time' => '12:00:00',
            'title' => 'ปรับปรุงระบบไฟส่องสว่าง LED',
            'description' => 'ช่างเข้าซ่อมและเปลี่ยนหลอดไฟด้านบนเพดานสนาม',
        ]);

        // 5. Sample Confirmed Booking (Tomorrow Court 1 18:00 - 20:00) for overlap testing
        $booking1 = Booking::create([
            'user_id' => $user1->id,
            'court_id' => $court1->id,
            'booking_date' => Carbon::tomorrow()->toDateString(),
            'start_time' => '18:00:00',
            'end_time' => '20:00:00',
            'total_hours' => 2.00,
            'court_price' => 300.00,
            'equipment_price' => 50.00,
            'grand_total' => 350.00,
            'status' => 'confirmed',
            'notes' => 'ต้องการตาข่ายตึงเป็นพิเศษ',
        ]);

        Payment::create([
            'booking_id' => $booking1->id,
            'amount' => 350.00,
            'payment_date' => Carbon::now(),
            'status' => 'paid',
            'admin_note' => 'อนุมัติสลิปโอนเงินเรียบร้อย ยอดตรง',
        ]);

        EquipmentRental::create([
            'booking_id' => $booking1->id,
            'equipment_id' => $eq1->id,
            'quantity' => 1,
            'unit_price' => 50.00,
            'subtotal' => 50.00,
        ]);

        // 6. Sample Matchmaking on Booking 1
        $match = PlayerMatch::create([
            'booking_id' => $booking1->id,
            'slots_needed' => 2,
            'slots_filled' => 1,
            'skill_level' => 'intermediate',
            'details' => 'หาเพื่อนเล่นประเภทคู่ 2 คน เน้นเล่นสนุกๆ ออกกำลังกายหลังเลิกงาน มีน้ำเย็นเลี้ยงครับ',
            'status' => 'open',
        ]);

        MatchMember::create([
            'player_match_id' => $match->id,
            'user_id' => $user2->id,
            'status' => 'accepted',
            'message' => 'ขอร่วมก๊วนด้วยคนครับ เล่นระดับกลางๆ ประจำสนามนี้ครับ',
        ]);

        // 7. Sample Completed Bookings in the past with Realistic Reviews
        $reviewsSeed = [
            ['court' => $court1, 'user' => $user2, 'rating' => 5, 'comment' => 'สนามสะอาดมาก พื้นยางหนึบกำลังดี ไฟสว่างทั่วถึง แอร์บริเวณโซนพักผ่อนเย็นฉ่ำ แนะนำเลยครับ!', 'days' => 3],
            ['court' => $court1, 'user' => $user1, 'rating' => 5, 'comment' => 'คอร์ทสวยมาตรฐาน BWF เลยครับ ตีมันส์มาก พื้นไม่ลื่น เสียเหงื่อสะใจ', 'days' => 2],
            ['court' => $court2, 'user' => $user1, 'rating' => 4, 'comment' => 'พื้นยางมาตรฐานดีมาก ไฟสว่าง แต่ช่วงเย็นคนเยอะหน่อย โดยรวมประทับใจครับ', 'days' => 4],
            ['court' => $court2, 'user' => $user2, 'rating' => 5, 'comment' => 'ระบบพัดลมระบายอากาศรอบทิศทางดีมาก ไม่อบอ้าวเลย ราคาคุ้มค่ามากค่ะ', 'days' => 1],
            ['court' => $court3, 'user' => $user1, 'rating' => 5, 'comment' => 'พื้นไวนิลซับแรงดีเยี่ยมมาก ตีแล้วไม่ปวดเข่าหรือข้อเท้าเลย คุ้มค่าราคามากครับ', 'days' => 5],
            ['court' => $court4, 'user' => $user2, 'rating' => 5, 'comment' => 'โซน VIP สบายมาก แอร์เย็นเจี๊ยบ โซฟานั่งพักดีงาม เหมาะกับมาตีกับเพื่อนกลุ่มใหญ่หรือครอบครัวค่ะ', 'days' => 6],
        ];

        foreach ($reviewsSeed as $rev) {
            $pastBooking = Booking::create([
                'user_id' => $rev['user']->id,
                'court_id' => $rev['court']->id,
                'booking_date' => Carbon::now()->subDays($rev['days'])->toDateString(),
                'start_time' => '18:00:00',
                'end_time' => '20:00:00',
                'total_hours' => 2.00,
                'court_price' => $rev['court']->price_per_hour * 2,
                'equipment_price' => 0.00,
                'grand_total' => $rev['court']->price_per_hour * 2,
                'status' => 'completed',
                'notes' => 'ใช้งานเรียบร้อย',
            ]);

            Payment::create([
                'booking_id' => $pastBooking->id,
                'amount' => $pastBooking->grand_total,
                'payment_date' => Carbon::now()->subDays($rev['days']),
                'status' => 'paid',
                'admin_note' => 'ชำระเงินเรียบร้อย',
            ]);

            Review::create([
                'booking_id' => $pastBooking->id,
                'user_id' => $rev['user']->id,
                'court_id' => $rev['court']->id,
                'rating' => $rev['rating'],
                'comment' => $rev['comment'],
            ]);
        }
    }
}
