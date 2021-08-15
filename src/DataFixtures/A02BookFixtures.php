<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Book;
use App\Enum\BookTag;

class A02BookFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $bookTagArray = array_values(BookTag::toArray());
        array_push($bookTagArray,null);
        $bookData = file_get_contents('src\DataFixtures\bookData.json');
        $bookList = json_decode($bookData, true);

        foreach ($bookList as $item) {
            $book = new Book();
            $book->setName($item['title']);
            $book->setDetail($item['summary']);
            $book->setTag([$bookTagArray[rand(0,2)]]);
            $book->setPrice($item['price']['displayValue']*33);
            $book->setAbout($item['author']);
            $book->setImagePath($item['image']);
            $book->setCreateAt(new \DateTimeImmutable());
            $book->setIsHidden(rand(true,false));
            $book->setDiscount(rand(0,$book->getPrice()));
            $manager->persist($book);
        }


        // for ($i=0; $i < 20; $i++) { 
        //     $book = new Book();
        //     $book->setName('ฟื้นคืน REVIVAL'.$i);
        //     $book->setDetail(
        //         $i.
        //         "ในเมืองเล็กๆ แห่งหนึ่ง เจมี มอร์ตัน เด็กชายตัวน้อย
        //         ได้พบกับ ชาร์ลส์ เจค็อบส์ สาธุคุณคนใหม่ของเมืองโดยบังเอิญ
        //         ทั้งสองคนสนิทสนมกันอย่างรวดเร็ว
        //         เพราะชื่อชอบการทดลองกระแสไฟฟ้าเหมือนกัน
        //         จนกระทั่งวันหนึ่งเกิดอุบัติเหตุอันน่าเศร้าขึ้นทำให้ทุกอย่างพลิกผัน
                
        //         หลายปีต่อมา ขณะที่เจมีเป็นนักดนตรี เขาได้เจอกับเจค็อบส์อีกครั้ง
        //         ทว่าการพบกันครั้งนี้กลับมีความหมายมากกว่าที่คิด
        //         ความผูกพันของพวกเขากลายเป็นพันธสัญญา
        //         ที่ร้ายกาจยิ่งกว่าแผนการของปีศาจ
        //         และเขาจะทำอย่างไรเมื่อสิ่งที่รออยู่เบื้องหลัง การฟื้นคืน นั้น
        //         กลับกลายเป็นความหวาดผวาเกินต้านทาน!"
        //         .$i
        //     );
        //     $book->setPrice(499);
        //     $book->setAbout(
        //         $i.
        //         "สตีเวน คิง คือนักเขียนชื่อดังที่ทุกคนต่างรู้จัก และเรื่อง ฟื้นคืน หรือ Revival ที่อยู่ในมือทุกท่านนี้ ก็คือผลงานที่ผู้อ่านทั่วโลกลงความเห็นว่าเป็นงานที่ดีที่สุดเรื่องหนึ่งของคิง
        //         เมื่อคนที่ศรัทธาในพระเจ้ากลับต้องสูญเสียครอบครัวอันเป็นที่รักยิ่งไปตลอดกาล ความศรัทธาจึงแปรเปลี่ยนเป็นความชิงชัง สตีเวน คิง ทำให้ผู้อ่านได้เห็นว่าความสิ้นหวังที่แท้จริงเป็นอย่างไร รวมทั้งพาทุกคนเข้าสู่ใจกลางของคำถามสำคัญที่ว่าโลกหลังความตายมีอยู่จริงหรือไม่                
        //         แพรวสำนักพิมพ์"
        //         .$i
        //     );
        //     $book->setImagePath('https://storage.naiin.com/system/application/bookstore/resource/product/202106/528559/1000241701_front_XXXL.jpg?imgname=%E0%B8%9F%E0%B8%B7%E0%B9%89%E0%B8%99%E0%B8%84%E0%B8%B7%E0%B8%99-REVIVAL');
        //     $manager->persist($book);
        // }
        
        $manager->flush();
    }
}
